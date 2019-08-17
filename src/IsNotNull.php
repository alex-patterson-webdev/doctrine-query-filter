<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

/**
 * IsNotNull
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class IsNotNull implements QueryFilterInterface
{
    /**
     * $fieldName
     *
     * @var string
     */
    protected $fieldName;

    /**
     * $alias
     *
     * @var string
     */
    protected $alias = '';

    /**
     * __construct
     *
     * @param string $fieldName
     * @param string $alias
     */
    public function __construct(string $fieldName, string $alias = null)
    {
        $this->fieldName = $fieldName;

        if ($alias) {
            $this->alias = $alias;
        }
    }

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
        $fieldName = $this->fieldName;
        $alias     = $this->alias;

        if (! empty($alias) && false === strpos($fieldName, '.')) {
            $fieldName = $alias . '.' . $fieldName;
        }

        return (string) (new Expr())->isNotNull($fieldName);
    }

}