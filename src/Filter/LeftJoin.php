<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;

final class LeftJoin extends AbstractJoin
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param string $fieldName
     * @param string $alias
     * @param null|string|Composite|Base $condition
     * @param JoinConditionType|null $joinConditionType
     * @param string|null $indexBy
     */
    protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        $condition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void {
        $queryBuilder->leftJoin(
            $fieldName,
            $alias,
            $joinConditionType,
            isset($condition) ? (string)$condition : null,
            $indexBy
        );
    }
}
