<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterManagerInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\QueryFilterManager
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\LaminasDoctrine
 */
final class QueryFilterManagerTest extends TestCase
{
    /**
     * @var FilterManagerInterface|MockObject
     */
    private $filterManager;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->filterManager = $this->createMock(FilterManagerInterface::class);
    }

    /**
     * Assert that the manager implements QueryFilterManagerInterface
     */
    public function testImplementsQueryFilterManagerInterface(): void
    {
        $manager = new QueryFilterManager($this->filterManager);

        $this->assertInstanceOf(QueryFilterManagerInterface::class, $manager);
    }

    /**
     * Assert no filtering will be applied if filter() is provided configuration without the required 'filters' key
     *
     * @throws QueryFilterManagerException
     */
    public function testFilterWillNotPerformFilteringWithoutFilterKey(): void
    {
        $queryFilterManager = new QueryFilterManager($this->filterManager);

        /** @var QueryBuilderInterface|MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $queryBuilder->expects($this->never())->method('getEntityManager');

        $this->assertSame($queryBuilder, $queryFilterManager->filter($queryBuilder, 'Foo', []));
    }
}
