<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Andx as DoctrineAndX;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Orx as DoctrineOrX;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
abstract class AbstractJoin extends AbstractFilter
{
    /**
     * @param QueryBuilderInterface      $queryBuilder
     * @param string                     $fieldName
     * @param string                     $alias
     * @param null|string|Composite|Base $condition
     * @param string                     $joinType
     * @param string|null                $indexBy
     */
    abstract protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        $condition = null,
        string $joinType = Join::WITH,
        ?string $indexBy = null
    ): void;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array<mixed>                 $criteria
     *
     * @throws InvalidArgumentException
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        $fieldName = $this->resolveFieldName($metadata, $criteria);
        $mapping = $this->getAssociationMapping($metadata, $fieldName);

        $queryAlias = $this->getAlias($queryBuilder, $criteria['alias'] ?? null);
        $conditions = $criteria['conditions'] ?? [];
        $condition = null;

        if (is_string($conditions)) {
            $condition = $conditions;
        } elseif (is_object($conditions)) {
            $condition = (string)$conditions;
        } elseif (is_array($conditions) && !empty($conditions)) {
            $tempQueryBuilder = $queryBuilder->createQueryBuilder();

            $this->filterJoinCriteria(
                $tempQueryBuilder,
                $mapping['targetEntity'],
                ['filters' => $this->createJoinFilters($conditions, $queryAlias, $criteria)]
            );

            $condition = $this->mergeJoinConditions($queryBuilder, $tempQueryBuilder);
        }

        $this->applyJoin(
            $queryBuilder,
            $queryBuilder->getRootAlias() . '.' . $fieldName,
            $queryAlias,
            $condition,
            $criteria['join_type'] ?? Join::WITH,
            $criteria['index_by'] ?? null
        );
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     *
     * @return  array<mixed>
     *
     * @throws InvalidArgumentException
     */
    private function getAssociationMapping(MetadataInterface $metadata, string $fieldName): array
    {
        try {
            return $metadata->getAssociationMapping($fieldName);
        } catch (MetadataException $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Failed to load association field mapping for field \'%s::%s\' in filter \'%s\'',
                    $metadata->getName(),
                    $fieldName,
                    static::class
                )
            );
        }
    }

    /**
     * @param QueryBuilderInterface $qb
     * @param string                $targetEntity
     * @param array<mixed>                 $criteria
     *
     * @throws FilterException
     */
    private function filterJoinCriteria(QueryBuilderInterface $qb, string $targetEntity, array $criteria): void
    {
        try {
            $this->queryFilterManager->filter($qb, $targetEntity, $criteria);
        } catch (QueryFilterManagerException $e) {
            throw new FilterException(
                sprintf(
                    'Failed to apply query filter \'%s\' conditions for target entity \'%s\': %s',
                    static::class,
                    $targetEntity,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param array<mixed>  $conditions
     * @param string $alias
     * @param array<mixed>  $criteria
     *
     * @return  array<mixed>
     */
    private function createJoinFilters(array $conditions, string $alias, array $criteria): array
    {
        // Use the join alias as the default alias for conditions
        foreach ($conditions as $index => $condition) {
            if (is_array($condition) && empty($condition['alias'])) {
                $conditions[$index]['alias'] = $alias;
            }
        }

        return [
            [
                'name'       => AndX::class,
                'conditions' => $conditions,
                'where'      => $criteria['filters']['where'] ?? null,
            ],
        ];
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param QueryBuilderInterface $qb
     *
     * @return DoctrineAndX|DoctrineOrX|null
     */
    private function mergeJoinConditions(QueryBuilderInterface $queryBuilder, QueryBuilderInterface $qb): ?Composite
    {
        $parts = $qb->getQueryParts();

        if (!isset($parts['where'])) {
            return null;
        }

        if ($parts['where'] instanceof DoctrineAndx) {
            $condition = $queryBuilder->expr()->andX();
        } elseif ($parts['where'] instanceof DoctrineOrX) {
            $condition = $queryBuilder->expr()->orX();
        } else {
            return null;
        }

        $condition->addMultiple($parts['where']->getParts());
        $queryBuilder->mergeParameters($qb);

        return $condition;
    }
}
