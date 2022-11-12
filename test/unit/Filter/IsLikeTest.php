<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLike;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLike
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsLikeTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsLike::class;

    protected string $expressionMethodName = 'like';

    protected string $expressionSymbol = 'LIKE';

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
