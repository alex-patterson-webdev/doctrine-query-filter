<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * Between
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Between implements QueryFilterInterface
{
    /**
     * $fieldName
     *
     * @var string
     */
    protected $fieldName;

    /**
     * $a
     *
     * @var mixed
     */
    protected $a;

    /**
     * $b
     *
     * @var mixed
     */
    protected $b;

    /**
     * __construct
     *
     * @param string $fieldName
     * @param mixed  $a
     * @param mixed  $b
     */
    public function __construct(string $fieldName, $a, $b)
    {
        $this->fieldName = $fieldName;
        $this->a         = $a;
        $this->b         = $b;
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
        return (string) (new Expr())->between($this->fieldName, $this->a, $this->b);
    }

}