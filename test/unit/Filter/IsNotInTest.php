<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotIn;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotIn
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsNotInTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsNotIn::class;

    protected string $expressionMethodName = 'notIn';

    protected string $expressionSymbol = 'NOT IN';

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
