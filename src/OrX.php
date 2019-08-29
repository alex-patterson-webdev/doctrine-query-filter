<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
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
     * @param QueryExpressionFactoryInterface $factory
     *
     * @return string
     */
    public function build(QueryExpressionFactoryInterface $factory): string
    {
        $orX = (new Expr())->orX();

        foreach($this->queryFilters as $queryFilter) {
            $orX->add($queryFilter->build($factory));
        }

        return (string) $orX;
    }


}