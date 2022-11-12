<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsEqual
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsEqualTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsEqual::class;

    protected string $expressionMethodName = 'eq';

    protected string $expressionSymbol = '=';

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
