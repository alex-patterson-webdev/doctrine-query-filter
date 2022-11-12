<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Enum\FieldType;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterFactory;
use Arp\DoctrineQueryFilter\Metadata\ArrayMapNameGenerator;
use Arp\DoctrineQueryFilter\Metadata\Typecaster;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use Arp\DoctrineQueryFilter\Sort\SortFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BuildQueryTest extends TestCase
{
    private FilterFactory $filterFactory;

    private SortFactory $sortFactory;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $fieldMapping = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $associationMapping = [];

    /**
     * @var array<string, string>
     */
    private array $parameterMapping = [
        'forename' => 'name1234',
        'from' => 'fromABC123',
        'to' => 'toXYZ987'
    ];

    public function setUp(): void
    {
        $this->fieldMapping = [
            'forename' => [
                'type' => FieldType::STRING->value,
            ],
            'age' => [
                'type' => FieldType::INTEGER->value,
            ],
        ];

        $this->filterFactory = new FilterFactory(
            new Typecaster(),
            new ArrayMapNameGenerator($this->parameterMapping)
        );

        $this->sortFactory = new SortFactory();
    }

    /**
     * @dataProvider getFilterCriteriaData
     *
     * @param string $expectedDql
     * @param array<mixed> $expectedParams
     * @param array<mixed> $criteria
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterCriteria(string $expectedDql, array $expectedParams, array $criteria): void
    {
        $entityName = 'Customer';
        $queryAlias = 'c';

        $doctrineMetadata = $this->createDoctrineMetadata();
        $entityManager = $this->createEntityManager();
        $entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($doctrineMetadata);

        $doctrineQueryBuilder = $this->createDoctrineQueryBuilder($entityManager)
            ->select($queryAlias)
            ->from($entityName, $queryAlias);

        $filterManager = new QueryFilterManager($this->filterFactory, $this->sortFactory);

        $queryBuilder = $filterManager->filter($doctrineQueryBuilder, $entityName, $criteria);

        $this->assertSame($expectedDql, $queryBuilder->getDQL());
    }

    /**
     * @return array<mixed>
     */
    public function getFilterCriteriaData(): array
    {
        return [
            [
                sprintf(
                    'SELECT c FROM Customer c WHERE c.forename = :%s AND (c.age BETWEEN :%s AND :%s)',
                    $this->parameterMapping['forename'],
                    $this->parameterMapping['from'],
                    $this->parameterMapping['to']
                ),
                [
                    $this->parameterMapping['forename'] => 'Fred',
                    $this->parameterMapping['from'] => 18,
                    $this->parameterMapping['to'] => 30,
                ],
                [
                    'filters' => [
                        [
                            'name' => 'eq',
                            'field' => 'forename',
                            'value' => 'Fred',
                        ],
                        [
                            'name' => 'between',
                            'field' => 'age',
                            'from' => 18,
                            'to' => 30,
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * @return ClassMetadata<object>
     */
    private function createDoctrineMetadata(): ClassMetadata
    {
        $doctrineMetadata = $this->createMock(ClassMetadata::class);

        $doctrineMetadata->method('hasField')
            ->willReturnCallback(fn(string $fieldName) => isset($this->fieldMapping[$fieldName]));

        $doctrineMetadata->method('hasAssociation')
            ->willReturnCallback(fn(string $fieldName) => isset($this->associationMapping[$fieldName]));

        $doctrineMetadata->method('getFieldMapping')
            ->willReturnCallback(fn(string $fieldName) => $this->fieldMapping[$fieldName] ?? []);

        return $doctrineMetadata;
    }

    private function createDoctrineQueryBuilder(EntityManager $entityManager): DoctrineQueryBuilder
    {
        return new DoctrineQueryBuilder($entityManager);
    }

    private function createEntityManager(): EntityManager&MockObject
    {
        $entityManager = $this->createMock(EntityManager::class);

        $entityManager->method('createQueryBuilder')
            ->willReturnCallback(fn() => $this->createDoctrineQueryBuilder($entityManager));

        $entityManager->method('getExpressionBuilder')
            ->willReturnCallback(static fn() => new Expr());

        return $entityManager;
    }
}
