<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotEqual
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsNotEqualTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsNotEqual::class;

    protected string $expressionMethodName = 'neq';

    protected string $expressionSymbol = '!=';

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
