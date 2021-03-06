<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotIn;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotIn
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsNotInTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsNotIn::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'notIn';

    /**
     * @var string
     */
    protected string $expressionSymbol = 'NOT IN';

    /**
     * @return array
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
