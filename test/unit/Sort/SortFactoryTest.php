<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortFactoryException;
use Arp\DoctrineQueryFilter\Sort\SortFactory;
use Arp\DoctrineQueryFilter\Sort\SortFactoryInterface;
use Arp\DoctrineQueryFilter\Sort\SortInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\Sort\SortFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Sort
 */
final class SortFactoryTest extends TestCase
{
    /**
     * @var QueryFilterManagerInterface&MockObject
     */
    private $queryFilterManager;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManagerInterface::class);
    }

    /**
     * Assert that the factory implements SortFactoryInterface
     */
    public function testImplementsSortFactoryInterface(): void
    {
        $factory = new SortFactory();

        $this->assertInstanceOf(SortFactoryInterface::class, $factory);
    }

    /**
     * Assert that a SortFactoryException will be thrown if the provided $name is invalid
     *
     * @throws SortFactoryException
     */
    public function testCreateWillThrowSortFactoryExceptionIfSortFilterNameCannotBeResolved(): void
    {
        $factory = new SortFactory();

        $name = 'foo';

        $this->expectException(SortFactoryException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The sort filter \'%s\' must be an object of type \'%s\'; '
                . 'The resolved class \'%s\' is invalid or cannot be found',
                $name,
                SortInterface::class,
                $name
            )
        );

        $factory->create($this->queryFilterManager, $name);
    }
}
