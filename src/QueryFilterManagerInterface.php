<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Sort\SortInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

interface QueryFilterManagerInterface
{
    /**
     * Generate a query builder instance constrained by the provided criteria
     *
     * @throws QueryFilterManagerException
     */
    public function filter(
        DoctrineQueryBuilder|QueryBuilderInterface $queryBuilder,
        string $entityName,
        array $criteria
    ): DoctrineQueryBuilder;

    /**
     * Apply the query filters to the provided query builder instance
     *
     * @throws QueryFilterManagerException
     */
    public function applyFilter(
        QueryBuilderInterface $queryBuilder,
        MetadataInterface $metadata,
        array|FilterInterface $data
    ): void;

    /**
     * Create a new filter instance by the provided name and options
     *
     * @throws QueryFilterManagerException
     */
    public function createFilter(string $name, array $options = []): FilterInterface;

    /**
     * Apply the query sort to the provided query builder instance
     *
     * @throws QueryFilterManagerException
     */
    public function applySort(
        QueryBuilderInterface $queryBuilder,
        MetadataInterface $metadata,
        array|SortInterface $data
    ): void;

    /**
     * Create a new sort instance by the provided name and options
     *
     * @throws QueryFilterManagerException
     */
    public function createSort(string $name, array $options = []): SortInterface;
}
