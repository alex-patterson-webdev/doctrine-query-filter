<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotNull;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotNull
 */
final class IsNotNullTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsNotNull::class;

    protected string $expressionMethodName = 'isnotnull';

    protected string $expressionSymbol = 'IS NOT NULL';

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
