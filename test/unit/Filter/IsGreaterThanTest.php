<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsGreaterThan;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsGreaterThan
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsGreaterThanTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsGreaterThan::class;

    protected string $expressionMethodName = 'gt';

    protected string $expressionSymbol = '>';

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
