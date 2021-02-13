<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\EntityManager;
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
}
