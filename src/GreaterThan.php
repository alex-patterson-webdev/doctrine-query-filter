<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * GreaterThan
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class GreaterThan extends AbstractExpression
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
    public function build(QueryExpressionFactoryInterface $factory) : string
    {
        return (string) (new Expr())->gt($this->a, $this->b);
    }

}