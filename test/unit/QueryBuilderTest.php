<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
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
     * @var DoctrineQueryBuilder
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

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        /** @var DoctrineQueryBuilder|MockObject $newDoctrineQueryBuilder */
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

        /** @var EntityManager|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManager::class);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('getEntityManager')
            ->willReturn($entityManager);

        $queryBuilder->getEntityManager();
    }

    /**
     * Assert that calls to expr() will return the internal query builder's Expr instance
     */
    public function testGetQueryBuilderWillReturnTheConfiguredExprInstance(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var Expr|MockObject $expr */
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
}
