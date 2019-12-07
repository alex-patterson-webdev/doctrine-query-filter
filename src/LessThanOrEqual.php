<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * LessThanOrEqual
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class LessThanOrEqual extends AbstractFilter
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
        return (string) (new Expr())->lte($this->a, $this->b);
    }
    
}