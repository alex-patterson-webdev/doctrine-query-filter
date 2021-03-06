<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\IsMemberOf;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsMemberOf
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsMemberOfTest extends AbstractComparisonTest
{
    /**
     * @var string
     */
    protected string $filterClassName = IsMemberOf::class;

    /**
     * @var string
     */
    protected string $expressionMethodName = 'isMemberOf';

    /**
     * @var string
     */
    protected string $expressionSymbol = 'MEMBER OF';

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
