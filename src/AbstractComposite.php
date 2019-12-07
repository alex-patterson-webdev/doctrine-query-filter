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
abstract class AbstractComposite extends AbstractQueryFilter
{
    /**
     * $queryFilters
     *
     * @var QueryFilterInterface[]
     */
    protected $queryFilters;

    /**
     * addMultiple
     *
     * Add multiple query filters to the collection.
     *
     * @param array $queryFilters
     */
    public function addMultiple(array $queryFilters)
    {
        foreach($queryFilters as $queryFilter) {
            $this->add($queryFilter);
        }
    }

    /**
     * add
     *
     * @param QueryFilterInterface $queryFilter
     */
    public function add(QueryFilterInterface $queryFilter)
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