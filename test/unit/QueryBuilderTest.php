<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Enum\OrderByDirection;
use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
final class QueryBuilderTest extends TestCase
{
    /**
     * @var DoctrineQueryBuilder&MockObject
     */
    private DoctrineQueryBuilder $doctrineQueryBuilder;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->doctrineQueryBuilder = $this->createMock(DoctrineQueryBuilder::class);
    }

    /**
     * Assert the class implement QueryBuilderInterface
     */
    public function testInstanceOfQueryBuilderInterface(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->assertInstanceOf(QueryBuilderInterface::class, $queryBuilder);
    }

    /**
     * Assert calls to createQueryBuilder will return a new instance of itself
     */
    public function testCreateQueryBuilderWillReturnAnewInstanceOfItself(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        /** @var DoctrineQueryBuilder&MockObject $newDoctrineQueryBuilder */
        $newDoctrineQueryBuilder = $this->createMock(DoctrineQueryBuilder::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($newDoctrineQueryBuilder);

        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder->createQueryBuilder());
    }

    /**
     * Assert that calls to getEntityManager will return the internal query builder's entity manager instance
     */
    public function testGetEntityManagerWillReturnTheConfiguredEntityManagerInstance(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var EntityManager&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $this->assertSame($entityManager, $queryBuilder->getEntityManager());
    }

    /**
     * Assert that the root alias can be set and fetched from setRootAlias() and getRootAlias()
     */
    public function testSetAndGetRootAlias(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $alias = 'test';

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn([]);

        $this->assertSame('', $queryBuilder->getRootAlias());

        $queryBuilder->setRootAlias($alias);

        $this->assertSame($alias, $queryBuilder->getRootAlias());
    }

    /**
     * Assert that the root alias returned from calls to getRootAlias()
     *
     * @dataProvider getGetRootAliasWillReturnQueryBuilderAliasAtIndexZeroData
     *
     * @param array<mixed> $aliases
     */
    public function testGetRootAliasWillReturnQueryBuilderAliasAtIndexZero(array $aliases = []): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        if (empty($aliases[0])) {
            $expected = '';
        } else {
            $expected = $aliases[0];
        }

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn($aliases);

        $this->assertSame($expected, $queryBuilder->getRootAlias());
    }

    /**
     * @return array<mixed>
     */
    public function getGetRootAliasWillReturnQueryBuilderAliasAtIndexZeroData(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'foo',
                ],
            ],
            [
                [
                    'baz',
                    'foo',
                    'bar',
                ],
            ],
        ];
    }

    /**
     * Assert that calls to getQuery() will proxy and return the wrapped query builders query instance
     */
    public function testGetWrappedQueryBuilderWillReturnTheDoctrineQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->assertSame($this->doctrineQueryBuilder, $queryBuilder->getWrappedQueryBuilder());
    }

    /**
     * Assert that calls to expr() will return the internal query builder's Expr instance
     */
    public function testGetQueryBuilderWillReturnTheConfiguredExprInstance(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var Expr&MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        $queryBuilder->expr();
    }

    /**
     * Assert that calls to expr() will return the internal query builder's DQL parts
     */
    public function testGetEntityManagerWillReturnTheConfiguredExprInstance(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $parts = [
            'foo' => 'bar',
        ];

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getDQLParts')
            ->willReturn($parts);

        $this->assertSame($parts, $queryBuilder->getQueryParts());
    }

    /**
     * Assert the Query instance is returned from getQuery()
     */
    public function testGetQuery(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var EntityManagerInterface&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        /** @var Configuration&MockObject $configuration */
        $configuration = $this->createMock(Configuration::class);

        $entityManager->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn($configuration);

        $configuration->expects($this->once())
            ->method('getDefaultQueryHints')
            ->willReturn([]);

        $configuration->expects($this->once())
            ->method('isSecondLevelCacheEnabled')
            ->willReturn(false);

        $query = new Query($entityManager);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->assertSame($query, $queryBuilder->getQuery());
    }

    /**
     * Assert that arguments passed to orWhere() will proxy to the internal query builder's orWhere() method
     *
     * @param array<mixed> $orWhere
     *
     * @dataProvider getWhereWillProxyToInternalQueryBuilderData
     */
    public function testOrWhereWillProxyToInternalQueryBuilder(array $orWhere): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('orWhere')
            ->with($orWhere);

        $this->assertSame($queryBuilder, $queryBuilder->orWhere($orWhere));
    }

    /**
     * Assert that arguments passed to andWhere() will proxy to the internal query builder's andWhere() method
     *
     * @param array<mixed> $orWhere
     *
     * @dataProvider getWhereWillProxyToInternalQueryBuilderData
     */
    public function testAndWhereWillProxyToInternalQueryBuilder(array $orWhere): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($orWhere);

        $this->assertSame($queryBuilder, $queryBuilder->andWhere($orWhere));
    }

    /**
     * @return array<mixed>
     */
    public function getWhereWillProxyToInternalQueryBuilderData(): array
    {
        $expr = new Expr();

        return [
            [
                [
                    '2 = 2',
                ],
            ],
            [
                [
                    'a.bar = :bar',
                    'a.test != :test AND b.foo = :foo',
                ],
            ],
            [
                [
                    $expr->eq('t.foo', ':foo'),
                    't.bar = 123',
                    $expr->eq('b.baz', ':baz'),
                ],
            ],
        ];
    }

    /**
     * Assert that calls to innerJoin() will proxy to the internal query builder
     */
    public function testInnerJoinWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $name = 'foo';
        $alias = 'a';
        $conditionType = JoinConditionType::ON;
        $condition = '1 = 1';
        $indexBy = null;

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with($name, $alias, $conditionType->value, $condition, $indexBy);

        $this->assertSame($queryBuilder, $queryBuilder->innerJoin($name, $alias, $conditionType, $condition, $indexBy));
    }

    /**
     * Assert that calls to leftJoin() will proxy to the internal query builder
     */
    public function testLeftJoinWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $name = 'bar';
        $alias = 'b';
        $conditionType = JoinConditionType::WITH;
        $condition = 'a.test = b.hello';
        $indexBy = 'a.name';

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('leftJoin')
            ->with($name, $alias, $conditionType->value, $condition, $indexBy);

        $this->assertSame($queryBuilder, $queryBuilder->leftJoin($name, $alias, $conditionType, $condition, $indexBy));
    }

    /**
     * Assert that calls to orderBy() will proxy to the internal query builder instance
     *
     * @param Expr\OrderBy|string $sort
     * @param OrderByDirection|null $direction
     *
     * @dataProvider getOrderByWillProxyToInternalQueryBuilderData
     */
    public function testOrderByWillProxyToInternalQueryBuilder(
        Expr\OrderBy|string $sort,
        ?OrderByDirection $direction = null
    ): void {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->assertSame($queryBuilder, $queryBuilder->orderBy($sort, $direction));
    }

    /**
     * Assert that calls to addOrderBy() will proxy to the internal query builder instance
     *
     * @param Expr\OrderBy|string $sort
     * @param OrderByDirection|null $direction
     *
     * @dataProvider getOrderByWillProxyToInternalQueryBuilderData
     */
    public function testAddOrderByWillProxyToInternalQueryBuilder(
        Expr\OrderBy|string $sort,
        ?OrderByDirection $direction = null
    ): void {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $this->assertSame($queryBuilder, $queryBuilder->addOrderBy($sort, $direction));
    }

    /**
     * @return array<mixed>
     */
    public function getOrderByWillProxyToInternalQueryBuilderData(): array
    {
        $expr = new Expr();

        return [
            [
                'a.test',
                OrderByDirection::DESC,
            ],
            [
                'b.foo',
                null,
            ],
            [
                $expr->asc('a.bar'),
            ],
            [
                $expr->desc('a.baz'),
                null,
            ],
        ];
    }

    /**
     * Assert that calls to getParameters() will proxy to the internal query builder
     */
    public function testGetParametersWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var ArrayCollection<int, Parameter>&MockObject $parameters */
        $parameters = $this->createMock(ArrayCollection::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getParameters')
            ->willReturn($parameters);

        $this->assertSame($parameters, $queryBuilder->getParameters());
    }

    /**
     * Assert that calls to setParameters() will proxy to the internal query builder
     */
    public function testSetParametersWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var ArrayCollection<int, Parameter>&MockObject $parameters */
        $parameters = $this->createMock(ArrayCollection::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($parameters);

        $this->assertSame($queryBuilder, $queryBuilder->setParameters($parameters));
    }

    /**
     * Assert that calls to setParameter() will proxy to the internal query builder
     */
    public function testSetParameterWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $name = 'Foo';
        $value = 'This is a test value';
        $type = Types::STRING;

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('setParameter')
            ->with($name, $value, $type);

        $this->assertSame($queryBuilder, $queryBuilder->setParameter($name, $value, $type));
    }

    /**
     * Assert that calls to mergeParameters() will proxy to the internal query builder
     */
    public function testMergeParametersWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $bParams = $addArgs = [];
        $b = [
            'bar'  => 456,
            'test' => 'This is value from B',
        ];
        $count = count($b);
        for ($x = 1; $x < $count; $x++) {
            /** @var Parameter&MockObject $parameter */
            $parameter = $this->createMock(Parameter::class);
            $bParams[] = $parameter;
            $addArgs[] = [$parameter];
        }

        /** @var ArrayCollection<int, Parameter>&MockObject $params */
        $params = $this->createMock(ArrayCollection::class);
        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getParameters')
            ->willReturn($params);

        /** @var QueryBuilderInterface&MockObject $newQueryBuilder */
        $newQueryBuilder = $this->createMock(QueryBuilderInterface::class);

        /** @var ArrayCollection<int, Parameter>&MockObject $newParams */
        $newParams = $this->createMock(ArrayCollection::class);

        $newQueryBuilder->expects($this->once())
            ->method('getParameters')
            ->willReturn($newParams);

        $newParams->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($bParams));

        $params->expects($this->exactly(count($bParams)))
            ->method('add')
            ->withConsecutive(...$addArgs);

        $this->assertSame($queryBuilder, $queryBuilder->mergeParameters($newQueryBuilder));
    }
}
