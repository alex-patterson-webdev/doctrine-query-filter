<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Enum\FieldType;
use Arp\DoctrineQueryFilter\Enum\OrderByDirection;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterFactory;
use Arp\DoctrineQueryFilter\Metadata\Typecaster;
use Arp\DoctrineQueryFilter\Metadata\UniqidParamNameGenerator;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use Arp\DoctrineQueryFilter\Sort\Field;
use Arp\DoctrineQueryFilter\Sort\SortFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BuildQueryTest extends TestCase
{
    private QueryFilterManager $queryFilterManager;

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $fieldMapping = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $associationMapping = [];

    public function setUp(): void
    {
        $this->fieldMapping = [
            'id' => [
                'type' => FieldType::INTEGER->value,
            ],
            'forename' => [
                'type' => FieldType::STRING->value,
            ],
            'username' => [
                'type' => FieldType::STRING->value,
            ],
            'age' => [
                'type' => FieldType::INTEGER->value,
            ],
            'enabled' => [
                'type' => FieldType::BOOLEAN->value,
            ],
            'createdDate' => [
                'type' => FieldType::DATETIME_MUTABLE->value,
            ]
        ];

        $this->queryFilterManager = new QueryFilterManager(
            new FilterFactory(new Typecaster(), new UniqidParamNameGenerator()),
            new SortFactory()
        );
    }

    /**
     * @dataProvider getFilterCriteriaData
     *
     * @param array<mixed> $criteria
     * @param string $expectedDql
     * @param array<mixed> $expectedParams
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterCriteria(array $criteria, string $expectedDql, array $expectedParams = []): void
    {
        $entityName = 'Customer';
        $queryAlias = 'c';

        $doctrineMetadata = $this->createDoctrineMetadata($entityName);
        $entityManager = $this->createEntityManager();
        $entityManager->method('getClassMetadata')
            ->with($entityName)
            ->willReturn($doctrineMetadata);

        $doctrineQueryBuilder = $this->createDoctrineQueryBuilder($entityManager)
            ->select($queryAlias)
            ->from($entityName, $queryAlias);

        $queryBuilder = $this->queryFilterManager->filter($doctrineQueryBuilder, $entityName, $criteria);

        $this->assertStringMatchesFormat($expectedDql, $queryBuilder->getDQL());

        if (!empty($expectedParams)) {
            /**
             * @var int $index
             * @var Parameter $parameter
             */
            foreach (array_values($queryBuilder->getParameters()->toArray()) as $index => $parameter) {
                if (!isset($expectedParams[$index])) {
                    $this->fail('Found unexpected parameter at index ' . $index);
                }
                $this->assertSame($expectedParams[$index], $parameter->getValue());
            }
        }
    }

    /**
     * @return array<mixed>
     */
    public function getFilterCriteriaData(): array
    {
        return [
            // Simple AND
            [
                [
                    'filters' => [
                        ['name' => 'eq', 'field' => 'forename', 'value' => 'Fred'],
                        ['name' => 'between', 'field' => 'age', 'from' => 18, 'to' => 30],
                    ],
                ],
                'SELECT c FROM Customer c WHERE c.forename = :%s AND (c.age BETWEEN :%s AND :%s)',
                ['Fred', 18, 30],
            ],

            // OR conditions
            [
                [
                    'filters' => [
                        ['name' => 'eq', 'field' => 'enabled', 'value' => true],
                        ['name' => 'orx',
                            'conditions' => [
                                ['name' => 'eq', 'field' => 'username', 'value' => 'Fred'],
                                ['name' => 'eq', 'field' => 'username', 'value' => 'bob'],
                            ]
                        ],
                    ],
                ],
                'SELECT c FROM Customer c WHERE c.enabled = :%s AND (c.username = :%s OR c.username = :%s)',
                [true, 'Fred', 'bob',],
            ],

            // Sorting
            [
                [
                    'filters' => [
                        ['name' => 'eq', 'field' => 'id', 'value' => 123],
                    ],
                    'sort' => [
                        ['name' => Field::class, 'field' => 'id', 'direction' => OrderByDirection::DESC->value],
                        ['field' => 'createdDate']
                    ]
                ],
                'SELECT c FROM Customer c WHERE c.id = :%s ORDER BY c.id DESC, c.createdDate ASC',
                [123]
            ],

            // Like
            [
                [
                    'filters' => [
                        [
                            'name' => 'orx',
                            'conditions' => [
                                ['name' => 'like', 'field' => 'forename', 'value' => '%Bob%'],
                                ['name' => 'gt', 'field' => 'age', 'value' => 18],
                            ]
                        ],
                        ['name' => 'eq', 'field' => 'enabled', 'value' => 1],
                    ]
                ],
                'SELECT c FROM Customer c WHERE (c.forename LIKE :%s OR c.age > :%s) AND c.enabled = :%s',
                ['%Bob%', 18, true],
            ]
        ];
    }

    /**
     * @param string $name
     *
     * @return ClassMetadata<object>
     */
    private function createDoctrineMetadata(string $name): ClassMetadata
    {
        $doctrineMetadata = $this->createMock(ClassMetadata::class);

        $doctrineMetadata->method('getName')
            ->willReturn($name);

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
