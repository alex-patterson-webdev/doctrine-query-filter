<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\IsEqual;
use Arp\DoctrineQueryFilter\Filter\IsNotEqual;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Arp\DoctrineQueryFilter\Sort\SortFactoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\QueryFilterManager
 */
final class QueryFilterManagerTest extends TestCase
{
    /**
     * @var FilterFactoryInterface&MockObject
     */
    private FilterFactoryInterface $filterFactory;

    /**
     * @var SortFactoryInterface&MockObject
     */
    private SortFactoryInterface $sortFactory;

    /**
     * @var EntityManagerInterface&MockObject
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var QueryBuilderInterface&MockObject
     */
    private QueryBuilderInterface $queryBuilder;

    /**
     * @var ClassMetadata<object>&MockObject
     */
    private ClassMetadata $metadata;

    private string $entityName;

    public function setUp(): void
    {
        $this->entityName = 'TestEntityName';

        $this->filterFactory = $this->createMock(FilterFactoryInterface::class);
        $this->sortFactory = $this->createMock(SortFactoryInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->metadata = $this->createMock(ClassMetadata::class);
    }

    /**
     * Assert that the manager implements QueryFilterManagerInterface
     */
    public function testImplementsQueryFilterManagerInterface(): void
    {
        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $this->assertInstanceOf(QueryFilterManagerInterface::class, $manager);
    }

    /**
     * Assert no filtering will be applied if filter() is provided configuration without the required 'filters' key
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterWillNotPerformFilteringWithoutFilterKey(): void
    {
        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willReturn($this->metadata);

        $this->filterFactory->expects($this->never())->method('create');

        $manager->filter($this->queryBuilder, $this->entityName, []);
    }

    /**
     * Assert that failure to create a entity metadata instance will result in a
     * QueryFilterManagerException being thrown
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterWillThrowQueryFilterManagerExceptionIfProvidedWithAnInvalidEntityName(): void
    {
        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $criteria = [
            'filters' => [
                [
                    'name' => 'foo',
                ],
            ],
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $exceptionCode = 123;
        $exception = new \Exception('This is an exception message', $exceptionCode);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willThrowException($exception);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(sprintf('Failed to fetch entity metadata for class \'%s\'', $this->entityName));

        $manager->filter($this->queryBuilder, $this->entityName, $criteria);
    }

    /**
     * Assert that a QueryFilterManagerException if thrown when providing an array query filter specification that
     * does not contain a 'name' property
     *
     * @throws QueryFilterManagerException
     */
    public function testMissingFilterNameWillThrowQueryFilterManagerException(): void
    {
        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $criteria = [
            'filters' => [
                [],
            ],
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willReturn($this->metadata);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'name\' configuration option is missing in \'%s\'', QueryFilterManager::class)
        );

        $manager->filter($this->queryBuilder, $this->entityName, $criteria);
    }

    /**
     * Assert that a QueryFilterManagerException is thrown when unable to create the filters in createFilter().
     *
     * @throws QueryFilterManagerException
     */
    public function testFailureToCreateFilterWillResultInQueryFilterManagerException(): void
    {
        $filterName = 'eq';
        $filterOptions = [
            'testing' => 123,
            'hello'   => 'world!',
        ];

        /** @var FilterInterface[]&MockObject[] $filters */
        $filters = [
            [
                'name'    => $filterName,
                'options' => $filterOptions, // Creation options
            ],
        ];

        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $criteria = [
            'filters' => $filters,
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willReturn($this->metadata);

        $exceptionCode = 456;
        $filterException = new FilterFactoryException(
            'This is a test filter factory exception message',
            $exceptionCode
        );

        $this->filterFactory->expects($this->once())
            ->method('create')
            ->with($manager, $filterName, $filterOptions)
            ->willThrowException($filterException);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(sprintf('Failed to create filter \'%s\'', $filterName));

        $manager->filter($this->queryBuilder, $this->entityName, $criteria);
    }

    /**
     * Assert that a QueryFilterManagerException is thrown when unable to apply the filters in applyFilter().
     *
     * @throws QueryFilterManagerException
     */
    public function testFailureToApplyFilterWillResultInQueryFilterManagerException(): void
    {
        /** @var FilterInterface[]&MockObject[] $filters */
        $filters = [
            $this->createMock(FilterInterface::class),
            $this->createMock(FilterInterface::class),
        ];

        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $criteria = [
            'filters' => $filters,
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willReturn($this->metadata);

        $exceptionCode = 999;
        $filterException = new FilterException('This is a test filter exception message', $exceptionCode);

        $filters[0]->expects($this->once())
            ->method('filter')
            ->with($this->queryBuilder, $this->isInstanceOf(MetadataInterface::class), [])
            ->willThrowException($filterException);

        $this->metadata->expects($this->once())
            ->method('getName')
            ->willReturn($this->entityName);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf('Failed to apply query filter for entity \'%s\'', $this->entityName)
        );

        $manager->filter($this->queryBuilder, $this->entityName, $criteria);
    }

    /**
     * Assert that the expected $criteria filters will be applied when calling filter()
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterApplyArrayFilterCriteria(): void
    {
        $filterData = [
            [
                'name'  => IsEqual::class,
                'field' => 'test',
                'value' => 123,
            ],
            [
                'name'  => IsNotEqual::class,
                'field' => 'test2',
                'value' => 'Hello World!',
            ],
        ];

        $manager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $criteria = [
            'filters' => $filterData,
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->entityName)
            ->willReturn($this->metadata);

        $factoryArgs = $createdFilters = [];
        foreach ($filterData as $data) {
            /** @var FilterInterface&MockObject $createdFilter */
            $createdFilter = $this->createMock(FilterInterface::class);

            $factoryArgs[] = [$manager, $data['name'], []];

            $createdFilter->expects($this->once())
                ->method('filter')
                ->with($this->queryBuilder, $this->isInstanceOf(MetadataInterface::class), $data);

            $createdFilters[] = $createdFilter;
        }

        $this->filterFactory->expects($this->exactly(count($filterData)))
            ->method('create')
            ->withConsecutive(...$factoryArgs)
            ->willReturnOnConsecutiveCalls(...$createdFilters);

        $manager->filter($this->queryBuilder, $this->entityName, $criteria);
    }
}
