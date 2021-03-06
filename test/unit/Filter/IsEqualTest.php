<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsEqual;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsEqual
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsEqualTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsEqual::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'eq';

    /**
     * @var string
     */
    protected string $expressionSymbol = '=';

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
