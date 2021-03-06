<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNotNull;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNotNull
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsNotNullTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsNotNull::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'isnotnull';

    /**
     * @var string
     */
    protected string $expressionSymbol = 'IS NOT NULL';

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
                    'value'=> 123,
                ],
            ],
        ];
    }
}
