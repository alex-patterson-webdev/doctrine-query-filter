<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsLessThanOrEqualTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsLessThanOrEqual::class;

    protected string $expressionMethodName = 'lte';

    protected string $expressionSymbol = '<=';

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
