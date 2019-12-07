<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * QueryFilterInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface QueryFilterInterface
{
    /**
     * filter
     *
     * Apply filtering to the provided query builder.
     *
     * @param QueryBuilderInterface  $queryBuilder  The query filter
     *
     * @throws QueryFilterException  If the query filtering cannot be applied
     */
    public function filter(QueryBuilderInterface $queryBuilder);

}