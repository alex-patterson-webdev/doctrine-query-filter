<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * QueryFilterInterface
 *
 * Perform custom filtering on a QueryBuilderInterface instance.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface QueryFilterInterface
{
    /**
     * filter
     *
     * Apply query filtering logic to the provided query builder.
     *
     * @param QueryBuilderInterface $queryBuilder
     */
    public function filter(QueryBuilderInterface $queryBuilder);

}