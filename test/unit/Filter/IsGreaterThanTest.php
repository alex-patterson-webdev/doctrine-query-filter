<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsGreaterThan;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsGreaterThan
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsGreaterThanTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsGreaterThan::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'gt';

    /**
     * @var string
     */
    protected string $expressionSymbol = '>';

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
