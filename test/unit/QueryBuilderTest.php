<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\AbstractQuery;
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

        $queryBuilder->getEntityManager();
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
     * Assert that calls to innerJoin() will proxy to the internal query builder
     */
    public function testInnerJoinWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $name = 'foo';
        $alias = 'a';
        $type = Expr\Join::ON;
        $condition = '1 = 1';
        $indexBy = null;

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with($name, $alias, $type, $condition, $indexBy);

        $this->assertSame($queryBuilder, $queryBuilder->innerJoin($name, $alias, $type, $condition, $indexBy));
    }

    /**
     * Assert that calls to leftJoin() will proxy to the internal query builder
     */
    public function testLeftJoinWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        $name = 'bar';
        $alias = 'b';
        $type = Expr\Join::ON;
        $condition = 'a.test = b.hello';
        $indexBy = 'a.name';

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('leftJoin')
            ->with($name, $alias, $type, $condition, $indexBy);

        $this->assertSame($queryBuilder, $queryBuilder->leftJoin($name, $alias, $type, $condition, $indexBy));
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
