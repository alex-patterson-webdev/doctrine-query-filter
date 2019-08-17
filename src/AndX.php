<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

/**
 * AndX
 *
 * Combine two or more expressions with and AND expression.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class AndX extends AbstractComposite
{
    /**
     * build
     *
     * Build the query filter expression.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder) : string
    {
        if (empty($this->queryFilters)) {
            return '';
        }

        $andX = (new Expr())->andX();

        foreach($this->queryFilters as $queryFilter) {
            $andX->add($queryFilter->build($queryBuilder));
        }

        return (string) $andX;
    }


}