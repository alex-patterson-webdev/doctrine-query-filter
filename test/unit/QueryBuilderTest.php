<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\QueryBuilder;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
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
}
