<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
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
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder): string
    {
        return (string) (new Expr())->notIn($this->fieldName, $this->collection);
    }

}