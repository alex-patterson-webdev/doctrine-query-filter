<?php

namespace Arp\DoctrineQueryFilter;

/**
 * AbstractExpression
 *
 * A simple comparative expression.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
abstract class AbstractExpression implements QueryExpressionInterface
{
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
     * @param mixed  $a
     * @param mixed  $b
     */
    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

}