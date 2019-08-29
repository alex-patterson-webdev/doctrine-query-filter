<?php

namespace Arp\DoctrineQueryFilter;

/**
 * Composite
 *
 * An expression that is composed of other expressions.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
abstract class AbstractComposite implements QueryExpressionInterface
{
    /**
     * $queryFilters
     *
     * @var QueryExpressionInterface[]
     */
    protected $queryFilters;

    /**
     * __construct
     *
     * @param array ...$queryFilters
     */
    public function __construct(...$queryFilters)
    {
        $this->queryFilters = $queryFilters;
    }

    /**
     * add
     *
     * @param QueryExpressionInterface $queryFilter
     */
    public function add(QueryExpressionInterface $queryFilter)
    {
        $this->queryFilters[] = $queryFilter;
    }

    /**
     * count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->queryFilters);
    }

}