<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNull;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNull
 */
final class IsNullTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsNull::class;

    protected string $expressionMethodName = 'isnull';

    protected string $expressionSymbol = 'IS NULL';

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
