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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \Arp\DoctrineQueryFilter\QueryFilterManager
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\LaminasDoctrine
 */
final class QueryFilterManagerTest extends TestCase
{
    /**
     * @var FilterFactoryInterface&MockObject
     */
    private $filterFactory;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->filterFactory = $this->createMock(FilterFactoryInterface::class);
    }

    /**
     * Assert that the manager implements QueryFilterManagerInterface
     */
    public function testImplementsQueryFilterManagerInterface(): void
    {
        $manager = new QueryFilterManager($this->filterFactory);

        $this->assertInstanceOf(QueryFilterManagerInterface::class, $manager);
    }

    /**
     * Assert no filtering will be applied if filter() is provided configuration without the required 'filters' key
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterWillNotPerformFilteringWithoutFilterKey(): void
    {
        $manager = new QueryFilterManager($this->filterFactory);

        /** @var DoctrineQueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(DoctrineQueryBuilder::class);

        $queryBuilder->expects($this->never())->method('getEntityManager');

        $this->assertSame($queryBuilder, $manager->filter($queryBuilder, 'Foo', []));
    }

    /**
     * Assert that failure to create a entity metadata instance will result in a
     * QueryFilterManagerException being thrown
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterWillThrowQueryFilterManagerExceptionIfProvidedWithAnInvalidEntityName(): void
    {
        $entityName = 'Test';
        $manager = new QueryFilterManager($this->filterFactory);

        $criteria = [
            'filters' => [
                [
                    'name' => 'foo',
                ],
            ],
        ];

        /** @var DoctrineQueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(DoctrineQueryBuilder::class);

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $exceptionMessage = 'This is an exception message';
        $exceptionCode = 123;
        $exception = new \Exception($exceptionMessage, $exceptionCode);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willThrowException($exception);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf('Failed to fetch entity metadata for class \'%s\': %s', $entityName, $exceptionMessage),
        );

        $manager->filter($queryBuilder, $entityName, $criteria);
    }

    /**
     * Assert that a QueryFilterManagerException is thrown if providing an invalid QueryBuilder instance to filter()
     *
     * @throws QueryFilterManagerException
     */
    public function testQueryFilterManagerExceptionIsThrownWhenProvidingAnInvalidQueryBuilderToFilter(): void
    {
        $manager = new QueryFilterManager($this->filterFactory);

        $invalidQueryBuilder = new \stdClass();
        $entityName = 'EntityTestName';
        $criteria = [];

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The \'queryBuilder\' argument must be an object of type \'%s\' or \'%s\'; '
                . '\'%s\' provided in \'%s\'',
                QueryBuilderInterface::class,
                DoctrineQueryBuilder::class,
                get_class($invalidQueryBuilder),
                QueryFilterManager::class
            )
        );

        /** @noinspection PhpParamsInspection */
        $manager->filter($invalidQueryBuilder, $entityName, $criteria); /** @phpstan-ignore-line */
    }

    /**
     * Assert that a QueryFilterManagerException if thrown when providing an array query filter specification that
     * does not contain a 'name' property
     *
     * @throws QueryFilterManagerException
     */
    public function testMissingFilterNameWillThrowQueryFilterManagerException(): void
    {
        $manager = new QueryFilterManager($this->filterFactory);

        /** @var QueryBuilderInterface&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $entityName = 'TestClass';
        $criteria = [
            'filters' => [
                [],
            ],
        ];

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        /** @var ClassMetadata&MockObject $metadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($metadata);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'name\' configuration option is missing in \'%s\'', QueryFilterManager::class)
        );

        $manager->filter($queryBuilder, $entityName, $criteria);
    }

    /**
     * Assert that a QueryFilterManagerException if thrown when providing invalid criteria filters to filter()
     *
     * @throws QueryFilterManagerException
     */
    public function testInvalidFilterWillThrowQueryFilterManagerException(): void
    {
        $manager = new QueryFilterManager($this->filterFactory);

        /** @var QueryBuilderInterface&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $filter = new \stdClass();
        $entityName = 'TestClass';
        $criteria = [
            'filters' => [
                $filter,
            ],
        ];

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        /** @var ClassMetadata&MockObject $metadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($metadata);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The \'data\' argument must be an \'array\' or object of type \'%s\'; \'%s\' provided in \'%s\'',
                FilterInterface::class,
                gettype($filter),
                QueryFilterManager::class
            )
        );

        $manager->filter($queryBuilder, $entityName, $criteria);
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
            'hello' => 'world!',
        ];

        /** @var FilterInterface[]&MockObject[] $filters */
        $filters = [
            [
                'name' => $filterName,
                'options' => $filterOptions, // Creation options
            ],
        ];

        $manager = new QueryFilterManager($this->filterFactory);

        /** @var QueryBuilderInterface&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $entityName = 'TestClass';
        $criteria = [
            'filters' => $filters,
        ];

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        /** @var ClassMetadata&MockObject $metadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($metadata);

        $exceptionMessage = 'This is a test filter factory exception message';
        $exceptionCode = 456;
        $filterException = new FilterFactoryException($exceptionMessage, $exceptionCode);

        $this->filterFactory->expects($this->once())
            ->method('create')
            ->with($manager, $filterName, $filterOptions)
            ->willThrowException($filterException);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf('Failed to create filter \'%s\': %s', $filterName, $exceptionMessage),
        );

        $manager->filter($queryBuilder, $entityName, $criteria);
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

        $manager = new QueryFilterManager($this->filterFactory);

        /** @var QueryBuilderInterface&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $entityName = 'TestClass';
        $criteria = [
            'filters' => $filters,
        ];

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        /** @var ClassMetadata&MockObject $metadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($metadata);

        $exceptionMessage = 'This is a test filter exception message';
        $exceptionCode = 999;
        $filterException = new FilterException($exceptionMessage, $exceptionCode);

        $filters[0]->expects($this->once())
            ->method('filter')
            ->with($queryBuilder, $this->isInstanceOf(MetadataInterface::class), [])
            ->willThrowException($filterException);

        $metadata->expects($this->once())
            ->method('getName')
            ->willReturn($entityName);

        $this->expectException(QueryFilterManagerException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf('Failed to apply query filter for entity \'%s\': %s', $entityName, $exceptionMessage)
        );

        $manager->filter($queryBuilder, $entityName, $criteria);
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

        $manager = new QueryFilterManager($this->filterFactory);

        /** @var QueryBuilderInterface&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $entityName = 'TestClass';
        $criteria = [
            'filters' => $filterData,
        ];

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $queryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        /** @var ClassMetadata&MockObject $metadata */
        $metadata = $this->createMock(ClassMetadata::class);

        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($metadata);

        $factoryArgs = $createdFilters = [];
        foreach ($filterData as $data) {
            /** @var FilterInterface&MockObject $createdFilter */
            $createdFilter = $this->createMock(FilterInterface::class);

            $factoryArgs[] = [$manager, $data['name'], $data['options'] ?? []];

            $createdFilter->expects($this->once())
                ->method('filter')
                ->with($queryBuilder, $this->isInstanceOf(MetadataInterface::class), $data);

            $createdFilters[] = $createdFilter;
        }

        $this->filterFactory->expects($this->exactly(count($filterData)))
            ->method('create')
            ->withConsecutive(...$factoryArgs)
            ->willReturnOnConsecutiveCalls(...$createdFilters);

        $manager->filter($queryBuilder, $entityName, $criteria);
    }
}
