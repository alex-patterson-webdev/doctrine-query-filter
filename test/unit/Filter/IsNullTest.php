<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsNull;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsNull
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsNullTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsNull::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'isnull';

    /**
     * @var string
     */
    protected string $expressionSymbol = 'IS NULL';

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
