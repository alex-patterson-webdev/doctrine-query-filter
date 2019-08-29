<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Doctrine\ORM\Query\Expr;

/**
 * Between
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Between implements QueryExpressionInterface
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
     * @param QueryExpressionFactoryInterface $factory
     *
     * @return string
     */
    public function build(QueryExpressionFactoryInterface $factory): string
    {
        return (string) (new Expr())->between($this->fieldName, $this->a, $this->b);
    }

}