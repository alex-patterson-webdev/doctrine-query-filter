<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

interface QueryFilterManagerInterface
{
    /**
     * Apply the query filters to the provided query builder instance
     *
     * @param DoctrineQueryBuilder|QueryBuilderInterface $queryBuilder
     * @param string $entityName
     * @param array<mixed> $criteria
     *
     * @return DoctrineQueryBuilder
     *
     * @throws QueryFilterManagerException
     */
    public function filter(
        DoctrineQueryBuilder|QueryBuilderInterface $queryBuilder,
        string $entityName,
        array $criteria
    ): DoctrineQueryBuilder;
}
