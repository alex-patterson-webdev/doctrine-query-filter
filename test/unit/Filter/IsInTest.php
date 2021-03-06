<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsIn;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsIn
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsInTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsIn::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'in';

    /**
     * @var string
     */
    protected string $expressionSymbol = 'IN';

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
