<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsGreaterThanOrEqualTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsGreaterThanOrEqual::class;

    protected string $expressionMethodName = 'gte';

    protected string $expressionSymbol = '>=';

    /**
     * @return array<mixed>
     */
    public function getFilterWillApplyFilteringData(): array
    {
        return [
            [
                [
                    'name' => 'test',
                    'field' => 'hello',
                    'value' => 123,
                ],
            ],
        ];
    }
}
