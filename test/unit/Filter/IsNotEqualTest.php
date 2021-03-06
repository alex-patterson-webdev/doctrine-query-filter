<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotEqual
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsNotEqualTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsNotEqual::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'neq';

    /**
     * @var string
     */
    protected string $expressionSymbol = '!=';

    /**
     * @return array
     */
    public function getFilterWillApplyFilteringData(): array
    {
        return [
            [
                [
                    'name' => 'test',
                    'field' => 'hello',
                    'value'=> 123,
                ],
            ],
        ];
    }
}
