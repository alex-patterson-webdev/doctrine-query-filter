<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
final class LeftJoin extends AbstractJoin
{
    /**
     * @param QueryBuilderInterface      $queryBuilder
     * @param string                     $fieldName
     * @param string                     $alias
     * @param null|string|Composite|Base $condition
     * @param string                     $joinType
     * @param string|null                $indexBy
     */
    protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        $condition = null,
        string $joinType = Join::WITH,
        ?string $indexBy = null
    ): void {
        $queryBuilder->leftJoin(
            $fieldName,
            $alias,
            $joinType,
            isset($condition) ? (string)$condition : null,
            $indexBy
        );
    }
}
