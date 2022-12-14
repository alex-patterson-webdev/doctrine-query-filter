<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Metadata\Metadata;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortException;
use Arp\DoctrineQueryFilter\Sort\Exception\SortFactoryException;
use Arp\DoctrineQueryFilter\Sort\Field;
use Arp\DoctrineQueryFilter\Sort\SortFactoryInterface;
use Arp\DoctrineQueryFilter\Sort\SortInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

class QueryFilterManager implements QueryFilterManagerInterface
{
    public function __construct(
        private readonly FilterFactoryInterface $filterFactory,
        private readonly SortFactoryInterface $sortFactory
    ) {
    }

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
    ): DoctrineQueryBuilder {
        $queryBuilder = $this->getQueryBuilder($queryBuilder);
        $metadata = $this->createMetadataProxy($queryBuilder->getEntityManager(), $entityName);

        foreach ($criteria['filters'] ?? [] as $filterCriteria) {
            $this->applyFilter($queryBuilder, $metadata, $filterCriteria);
        }

        foreach ($criteria['sort'] ?? [] as $sortCriteria) {
            $this->applySort($queryBuilder, $metadata, $sortCriteria);
        }

        return $queryBuilder->getWrappedQueryBuilder();
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed>|FilterInterface $data
     *
     * @throws QueryFilterManagerException
     */
    private function applyFilter(
        QueryBuilderInterface $queryBuilder,
        MetadataInterface $metadata,
        array|FilterInterface $data
    ): void {
        if ($data instanceof FilterInterface) {
            $filter = $data;
            $data = [];
        } else {
            $filterName = $data['name'] ?? null;

            if (empty($filterName)) {
                throw new QueryFilterManagerException(
                    sprintf('The required \'name\' configuration option is missing in \'%s\'', static::class)
                );
            }

            $filter = $this->createFilter($filterName, $data['options'] ?? []);
        }

        try {
            $filter->filter($queryBuilder, $metadata, $data);
        } catch (FilterException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to apply query filter for entity \'%s\'', $metadata->getName()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $name
     * @param array<mixed> $options
     *
     * @return FilterInterface
     *
     * @throws QueryFilterManagerException
     */
    private function createFilter(string $name, array $options = []): FilterInterface
    {
        try {
            return $this->filterFactory->create($this, $name, $options);
        } catch (FilterFactoryException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to create filter \'%s\'', $name),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed>|SortInterface $data
     *
     * @throws QueryFilterManagerException
     */
    private function applySort(
        QueryBuilderInterface $queryBuilder,
        MetadataInterface $metadata,
        array|SortInterface $data
    ): void {
        if ($data instanceof SortInterface) {
            $sort = $data;
            $data = [];
        } else {
            $sort = $this->createSort(
                empty($data['name']) ? Field::class : $data['name'],
                $data['options'] ?? []
            );
        }

        try {
            $sort->sort($queryBuilder, $metadata, $data);
        } catch (SortException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to apply query sorting for entity \'%s\'', $metadata->getName()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $name
     * @param array<mixed> $options
     *
     * @return SortInterface
     *
     * @throws QueryFilterManagerException
     */
    private function createSort(string $name, array $options = []): SortInterface
    {
        try {
            return $this->sortFactory->create($this, $name, $options);
        } catch (SortFactoryException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to create filter \'%s\'', $name),
                $e->getCode(),
                $e
            );
        }
    }

    private function getQueryBuilder(QueryBuilderInterface|DoctrineQueryBuilder $queryBuilder): QueryBuilderInterface
    {
        if ($queryBuilder instanceof DoctrineQueryBuilder) {
            $queryBuilder = new QueryBuilder($queryBuilder, $queryBuilder->getRootAliases()[0] ?? '');
        }

        return $queryBuilder;
    }

    /**
     * @throws QueryFilterManagerException
     */
    private function createMetadataProxy(EntityManagerInterface $entityManager, string $entityName): MetadataInterface
    {
        try {
            return new Metadata($entityManager->getClassMetadata($entityName));
        } catch (\Exception $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to fetch entity metadata for class \'%s\'', $entityName),
                $e->getCode(),
                $e
            );
        }
    }
}
