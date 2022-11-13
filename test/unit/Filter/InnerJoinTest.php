<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Filter\InnerJoin;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\InnerJoin
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractJoin
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class InnerJoinTest extends AbstractJoinTest
{
    protected string $filterClassName = InnerJoin::class;

    /**
     * @param QueryBuilderInterface&MockObject $queryBuilder
     * @param string $fieldName
     * @param string $alias
     * @param null|string|Composite|Base $joinCondition
     * @param JoinConditionType|null $joinConditionType
     * @param string|null $indexBy
     */
    protected function assertFilterJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        null|string|Composite|Base $joinCondition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void {
        $queryBuilder->expects($this->once())
            ->method('innerJoin')
            ->with($fieldName, $alias, $joinConditionType, $joinCondition, $indexBy);
    }
}
