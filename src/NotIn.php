<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * NotIn
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class NotIn extends AbstractFunction
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
        return (string) (new Expr())->notIn($this->fieldName, $this->collection);
    }

}