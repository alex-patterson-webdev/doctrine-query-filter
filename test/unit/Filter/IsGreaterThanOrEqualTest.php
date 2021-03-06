<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsGreaterThanOrEqualTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsGreaterThanOrEqual::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'gte';

    /**
     * @var string
     */
    protected string $expressionSymbol = '>=';

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
