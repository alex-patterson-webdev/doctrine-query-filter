<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

/**
 * OrX
 *
 * Combine two or more expressions with and OR expression.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class OrX extends AbstractComposite
{
    /**
     * build
     *
     * Build the query filter expression.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @param array                 $criteria
     *
     * @return string
     */
    public function filter(QueryBuilderInterface $queryBuilder, array $criteria)
    {
        $orX = (new Expr())->orX();

        foreach($this->queryFilters as $queryFilter) {
            $orX->add($queryFilter->filter($queryBuilder, $criteria));
        }

        return (string) $orX;
    }


}