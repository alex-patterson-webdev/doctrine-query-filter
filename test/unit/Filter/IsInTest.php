<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsIn;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsIn
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsInTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsIn::class;

    protected string $expressionMethodName = 'in';

    protected string $expressionSymbol = 'IN';

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
