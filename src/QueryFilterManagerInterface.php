<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface QueryFilterManagerInterface
{
    /**
     * Apply the query filters to the provided query builder instance
     *
     * @param DoctrineQueryBuilder|QueryBuilderInterface $queryBuilder
     * @param string                                     $entityName
     * @param array                                      $criteria
     *
     * @return QueryBuilderInterface
     *
     * @throws QueryFilterManagerException
     */
    public function filter($queryBuilder, string $entityName, array $criteria): QueryBuilderInterface;

    /**
     * Create a new filter matching $name with the provided $options
     *
     * @param string $name
     * @param array  $options
     *
     * @return FilterInterface
     *
     * @throws QueryFilterManagerException
     */
    public function createFilter(string $name, array $options = []): FilterInterface;
}
