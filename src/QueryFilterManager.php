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

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class QueryFilterManager implements QueryFilterManagerInterface
{
    private FilterFactoryInterface $filterFactory;

    private SortFactoryInterface $sortFactory;

    public function __construct(FilterFactoryInterface $filterFactory, SortFactoryInterface $sortFactory)
    {
        $this->filterFactory = $filterFactory;
        $this->sortFactory = $sortFactory;
    }

    /**
     * Apply the query filters to the provided query builder instance
     *
     * @param DoctrineQueryBuilder|QueryBuilderInterface $queryBuilder
     * @param string                                     $entityName
     * @param array<mixed>                               $criteria
     *
     * @return DoctrineQueryBuilder
     *
     * @throws QueryFilterManagerException
     */
    public function filter($queryBuilder, string $entityName, array $criteria): DoctrineQueryBuilder
    {
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
     * @param QueryBuilderInterface        $queryBuilder
     * @param MetadataInterface            $metadata
     * @param array<mixed>|FilterInterface $data
     *
     * @throws QueryFilterManagerException
     */
    private function applyFilter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, $data): void
    {
        if ($data instanceof FilterInterface) {
            $filter = $data;
            $data = [];
        } elseif (is_array($data)) {
            $filterName = $data['name'] ?? null;

            if (empty($filterName)) {
                throw new QueryFilterManagerException(
                    sprintf('The required \'name\' configuration option is missing in \'%s\'', static::class)
                );
            }

            $filter = $this->createFilter($filterName, $data['options'] ?? []);
        } else {
            throw new QueryFilterManagerException(
                sprintf(
                    'The \'data\' argument must be an \'array\' or object of type \'%s\'; \'%s\' provided in \'%s\'',
                    FilterInterface::class,
                    gettype($data),
                    static::class
                )
            );
        }

        try {
            $filter->filter($queryBuilder, $metadata, $data);
        } catch (FilterException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to apply query filter for entity \'%s\': %s', $metadata->getName(), $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create a new filter matching $name with the provided $options
     *
     * @param string       $name
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
                sprintf('Failed to create filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param QueryBuilderInterface            $queryBuilder
     * @param MetadataInterface                $metadata
     * @param array<mixed>|SortInterface|mixed $data
     *
     * @throws QueryFilterManagerException
     */
    private function applySort(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, $data): void
    {
        if ($data instanceof SortInterface) {
            $sort = $data;
            $data = [];
        } elseif (is_array($data)) {
            $sort = $this->createSort(
                empty($data['name']) ? Field::class : $data['name'],
                $data['options'] ?? []
            );
        } else {
            throw new QueryFilterManagerException(
                sprintf(
                    'The \'data\' argument must be an \'array\' or object of type \'%s\'; \'%s\' provided in \'%s\'',
                    SortInterface::class,
                    is_object($data) ? get_class($data) : gettype($data),
                    static::class
                )
            );
        }

        try {
            $sort->sort($queryBuilder, $metadata, $data);
        } catch (SortException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to apply query sorting for entity \'%s\': %s', $metadata->getName(), $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create a new sorting filter matching $name with the provided $options
     *
     * @param string       $name
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
                sprintf('Failed to create filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param QueryBuilderInterface|DoctrineQueryBuilder $queryBuilder
     *
     * @return QueryBuilderInterface
     *
     * @throws QueryFilterManagerException
     */
    private function getQueryBuilder($queryBuilder): QueryBuilderInterface
    {
        if ($queryBuilder instanceof DoctrineQueryBuilder) {
            $queryBuilder = new QueryBuilder($queryBuilder);
        }

        if (!$queryBuilder instanceof QueryBuilderInterface) {
            throw new QueryFilterManagerException(
                sprintf(
                    'The \'queryBuilder\' argument must be an object of type \'%s\' or \'%s\'; '
                    . '\'%s\' provided in \'%s\'',
                    QueryBuilderInterface::class,
                    DoctrineQueryBuilder::class,
                    get_class($queryBuilder),
                    static::class
                )
            );
        }

        return $queryBuilder;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string                 $entityName
     *
     * @return MetadataInterface
     *
     * @throws QueryFilterManagerException
     */
    private function createMetadataProxy(EntityManagerInterface $entityManager, string $entityName): MetadataInterface
    {
        try {
            return new Metadata($entityManager->getClassMetadata($entityName));
        } catch (\Throwable $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to fetch entity metadata for class \'%s\': %s', $entityName, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
