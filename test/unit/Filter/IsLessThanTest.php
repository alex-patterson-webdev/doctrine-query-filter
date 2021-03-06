<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsLessThan;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsLessThan
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsLessThanTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsLessThan::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'lt';

    /**
     * @var string
     */
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
