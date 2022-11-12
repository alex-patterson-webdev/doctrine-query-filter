<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Andx as DoctrineAndX;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\Query\Expr\Orx as DoctrineOrX;

abstract class AbstractJoin extends AbstractFilter
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param string $fieldName
     * @param string $alias
     * @param null|string|Composite|Base $condition
     * @param JoinConditionType|null $joinConditionType
     * @param string|null $indexBy
     */
    abstract protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        mixed $condition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed> $criteria
     *
     * @throws InvalidArgumentException
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        $fieldName = $this->resolveFieldName($metadata, $criteria);
        $queryAlias = $this->getAlias($queryBuilder, $criteria['alias'] ?? null);

        $condition = null;
        if (isset($criteria['conditions'])) {
            $mapping = $this->getAssociationMapping($metadata, $fieldName);
            $condition = $this->getCondition(
                $queryBuilder,
                $mapping['targetEntity'],
                $queryAlias,
                $criteria['conditions']
            );
        }

        $conditionType = is_string($criteria['condition_type'])
            ? JoinConditionType::tryFrom($criteria['condition_type'])
            : $criteria['condition_type'];

        $this->applyJoin(
            $queryBuilder,
            $queryBuilder->getRootAlias() . '.' . $fieldName,
            $queryAlias,
            $condition,
            $conditionType,
            $criteria['index_by'] ?? null
        );
    }

    /**
     * @throws FilterException
     */
    private function getCondition(
        QueryBuilderInterface $queryBuilder,
        string $targetEntity,
        string $queryAlias,
        mixed $conditions
    ): ?string {
        if (is_string($conditions)) {
            return $conditions;
        }

        if ($conditions instanceof Base) {
            return (string)$conditions;
        }

        $condition = null;
        if (is_array($conditions)) {
            $tempQueryBuilder = $this->filterJoinCriteria(
                $queryBuilder->createQueryBuilder(),
                $targetEntity,
                ['filters' => $this->createJoinFilters($conditions, $queryAlias)]
            );
            $condition = $this->mergeJoinConditions($queryBuilder, $tempQueryBuilder);
        }

        return isset($condition) ? (string)$condition : null;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $fieldName
     *
     * @return array<mixed>
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
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param QueryBuilderInterface $qb
     * @param string $targetEntity
     * @param array<mixed> $criteria
     *
     * @return QueryBuilderInterface
     * @throws FilterException
     */
    private function filterJoinCriteria(
        QueryBuilderInterface $qb,
        string $targetEntity,
        array $criteria
    ): QueryBuilderInterface {
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

        return $qb;
    }

    /**
     * @param array<mixed> $conditions
     * @param string $alias
     * @param array<mixed> $criteria
     *
     * @return array<mixed>
     */
    private function createJoinFilters(array $conditions, string $alias, array $criteria = []): array
    {
        // Use the join alias as the default alias for conditions
        foreach ($conditions as $index => $condition) {
            if (is_array($condition) && empty($condition['alias'])) {
                $conditions[$index]['alias'] = $alias;
            }
        }

        return [
            [
                'name' => AndX::class,
                'conditions' => $conditions,
                'where' => $criteria['filters']['where'] ?? null,
            ],
        ];
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param QueryBuilderInterface $qb
     *
     * @return Composite|null
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
