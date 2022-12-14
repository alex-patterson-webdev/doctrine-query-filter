<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLessThan;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLessThan
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsLessThanTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsLessThan::class;

    protected string $expressionMethodName = 'lt';

    protected string $expressionSymbol = '<';

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
