<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\FilterFactory;
use Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\IsEqual;
use Arp\DoctrineQueryFilter\Filter\IsNotEqual;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \Arp\DoctrineQueryFilter\Filter\FilterFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class FilterFactoryTest extends TestCase
{
    /**
     * @var QueryFilterManagerInterface|MockObject
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
     * Assert that the factory implements FilterFactoryInterface
     */
    public function testImplementsFilterFactory(): void
    {
        $factory = new FilterFactory();

        $this->assertInstanceOf(FilterFactoryInterface::class, $factory);
    }

    /**
     * Assert the expected query filter is created using the provided $name and $options and optional $classMap.
     *
     * @param string $expected
     * @param string $name
     * @param array  $options
     * @param array  $classMap
     *
     * @dataProvider getCreateWillReturnFilterInstanceData
     */
    public function testCreateWillReturnFilterInstance(
        string $expected,
        string $name,
        array $options = [],
        array $classMap = []
    ): void {
        $factory = new FilterFactory($classMap);

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf($expected, $factory->create($this->queryFilterManager, $name, $options));
    }

    /**
     * @return array
     */
    public function getCreateWillReturnFilterInstanceData(): array
    {
        return [
            [IsEqual::class, 'eq'],
            [IsEqual::class, IsEqual::class],

            [IsNotEqual::class, 'neq'],
            [IsNotEqual::class, IsNotEqual::class],
        ];
    }
}
