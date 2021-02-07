<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\FilterManagerInterface;
use Arp\DoctrineQueryFilter\Metadata\Metadata;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class QueryFilterManager implements QueryFilterManagerInterface
{
    /**
     * @var FilterManagerInterface
     */
    private FilterManagerInterface $filterManager;

    /**
     * @param FilterManagerInterface $filterManager
     */
    public function __construct(FilterManagerInterface $filterManager)
    {
        $this->filterManager = $filterManager;
    }

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
    public function filter($queryBuilder, string $entityName, array $criteria): QueryBuilderInterface
    {
        $queryBuilder = $this->getQueryBuilder($queryBuilder);

        if (!empty($criteria['filters']) && is_array($criteria['filters'])) {
            $metadata = $this->createMetadataProxy($queryBuilder->getEntityManager(), $entityName);
            foreach ($criteria['filters'] as $data) {
                $this->applyFilter($queryBuilder, $metadata, $data);
            }
        }

        return $queryBuilder;
    }

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
    public function createFilter(string $name, array $options = []): FilterInterface
    {
        try {
            return $this->filterManager->create($this, $name, $options);
        } catch (\Throwable $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to build query filter \'%s\': %s', $name, $e->getMessage()),
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
            $queryBuilder = $this->createQueryBuilderProxy($queryBuilder);
        }

        if (!$queryBuilder instanceof QueryBuilderInterface) {
            throw new QueryFilterManagerException(
                sprintf(
                    'The \'queryBuilder\' argument must be an object of type \'%s\' or \'%s\'; '
                    . '\'%s\' provided in \'%s\'',
                    QueryBuilderInterface::class,
                    DoctrineQueryBuilder::class,
                    is_object($queryBuilder) ? get_class($queryBuilder) : gettype($queryBuilder),
                    static::class
                )
            );
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array                 $data
     *
     * @throws QueryFilterManagerException
     */
    private function applyFilter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $data): void
    {
        $filterName = $data['name'] ?? null;

        if (empty($filterName)) {
            throw new QueryFilterManagerException(
                sprintf('The required \'name\' query filter configuration option is missing in \'%s\'', __METHOD__)
            );
        }

        $filter = $this->createFilter($filterName, $data['options'] ?? []);

        try {
            $filter->filter($queryBuilder, $metadata, $data);
        } catch (QueryFilterException $e) {
            throw new QueryFilterManagerException(
                sprintf('Failed to apply query filter for entity \'%s\': %s', $metadata->getName(), $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param EntityManager $entityManager
     * @param string        $entityName
     *
     * @return MetadataInterface
     *
     * @throws QueryFilterManagerException
     */
    private function createMetadataProxy(EntityManager $entityManager, string $entityName): MetadataInterface
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

    /**
     * @param DoctrineQueryBuilder $queryBuilder
     *
     * @return QueryBuilderInterface
     */
    private function createQueryBuilderProxy(DoctrineQueryBuilder $queryBuilder): QueryBuilderInterface
    {
        return new QueryBuilder($queryBuilder);
    }
}
