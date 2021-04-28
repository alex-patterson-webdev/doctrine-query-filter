<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLike;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLike
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsLikeTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsLike::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'like';

    /**
     * @var string
     */
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
