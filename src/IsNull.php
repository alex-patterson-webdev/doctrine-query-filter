<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * IsNull
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class IsNull implements QueryExpressionInterface
{
    /**
     * $fieldName
     *
     * @var string
     */
    protected $fieldName;

    /**
     * __construct
     *
     * @param string $fieldName
     * @param string $alias
     */
    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

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
        return (string) (new Expr())->isNull($this->fieldName);
    }

}