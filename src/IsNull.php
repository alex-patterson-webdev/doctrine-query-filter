<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * IsNull
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class IsNull implements QueryFilterInterface
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
     * @param QueryBuilderInterface $queryBuilder
     *
     * @param array                 $criteria
     *
     * @return string
     */
    public function filter(QueryBuilderInterface $queryBuilder, array $criteria)
    {
        return (string) (new Expr())->isNull($this->fieldName);
    }

}