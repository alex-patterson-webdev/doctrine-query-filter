<?php

namespace Arp\DoctrineQueryFilter;

/**
 * AbstractFunction
 *
 * An expression that performs a function with an optional collection of arguments.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
abstract class AbstractFunction implements QueryExpressionInterface
{
    /**
     * $fieldName
     *
     * @var string
     */
    protected $fieldName;

    /**
     * $collection
     *
     * @var array|string
     */
    protected $collection;

    /**
     * $alias
     *
     * @var string
     */
    protected $alias = '';

    /**
     * __construct
     *
     * @param string      $fieldName
     * @param array       $collection
     * @param string|null $alias
     */
    public function __construct(string $fieldName, array $collection, string $alias = null)
    {
        $this->fieldName  = $fieldName;
        $this->collection = $collection;

        if ($alias) {
            $this->alias = $alias;
        }
    }

}