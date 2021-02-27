<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
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

    /**
     * Assert that calls to getParameters() will proxy to the internal query builder
     */
    public function testGetParametersWillProxyToInternalQueryBuilder(): void
    {
        $queryBuilder = new QueryBuilder($this->doctrineQueryBuilder);

        /** @var ArrayCollection|MockObject $parameters */
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

        /** @var ArrayCollection|MockObject $parameters */
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

        /** @var QueryBuilderInterface|MockObject $newQueryBuilder */
        $newQueryBuilder = $this->createMock(QueryBuilderInterface::class);

        $a = [
            'baz' => 'Testing value',
            'foo' => 123,
            'test' => 'This is value from A'
        ];

        $b = [
            'bar' => 456,
            'test' => 'This is value from B'
        ];

        /**
         * @var ArrayCollection|MockObject $aParams
         */
        $aParams = $this->createMock(ArrayCollection::class);
        $aParams->expects($this->once())->method('toArray')->willReturn($a);

        /** @var ArrayCollection|MockObject $bParams */
        $bParams = $this->createMock(ArrayCollection::class);
        $newQueryBuilder->expects($this->once())->method('getParameters')->willReturn($bParams);
        $bParams->expects($this->once())->method('toArray')->willReturn($b);

        $expectedParams = array_replace_recursive($a, $b);

        $this->doctrineQueryBuilder->expects($this->once())
            ->method('setParameters')
            ->with($this->callback(function ($params) use ($expectedParams) {
                return ($params instanceof ArrayCollection && $params->toArray() === $expectedParams);
            }));

        /** @var ArrayCollection|MockObject $newParams */
        $newParams = $this->createMock(ArrayCollection::class);
        $this->doctrineQueryBuilder->expects($this->exactly(2))
            ->method('getParameters')
            ->willReturnOnConsecutiveCalls($aParams, $newParams);

        $newParams->expects($this->once())
            ->method('toArray')
            ->willReturn($expectedParams);

        $this->assertSame($queryBuilder, $queryBuilder->mergeParameters($newQueryBuilder));
        $this->assertSame($expectedParams, $queryBuilder->getParameters()->toArray());
    }
}
