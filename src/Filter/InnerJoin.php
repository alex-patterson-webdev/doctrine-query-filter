<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;

final class InnerJoin extends AbstractJoin
{
    protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        string|Composite|Base|null $condition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void {
        $queryBuilder->innerJoin(
            $fieldName,
            $alias,
            $joinConditionType,
            isset($condition) ? (string) $condition : null,
            $indexBy
        );
    }
}
