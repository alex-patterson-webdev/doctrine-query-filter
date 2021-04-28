<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsLessThanOrEqualTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsLessThanOrEqual::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'lte';

    /**
     * @var string
     */
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
