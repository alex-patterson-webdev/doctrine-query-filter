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
    abstract protected function applyJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        string|Composite|Base|null $condition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void;

    /**
     * @throws InvalidArgumentException
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        $fieldName = $this->resolveFieldName($metadata, $criteria);
        $fieldAlias = $this->resolveFieldAlias($queryBuilder, $criteria['field']);

        $joinAlias = $criteria['alias'] ?? null;
        if (null === $joinAlias) {
            throw new InvalidArgumentException(
                sprintf(
                    'The required \'alias\' criteria value is missing for filter \'%s\'',
                    static::class
                )
            );
        }

        $condition = null;
        $conditionType = null;

        if (isset($criteria['conditions'])) {
            $mapping = $this->getAssociationMapping($metadata, $fieldName);
            $condition = $this->getCondition(
                $queryBuilder,
                $mapping['targetEntity'],
                $joinAlias,
                $criteria['conditions']
            );

            $conditionType = $criteria['condition_type'] ?? null;
            if (is_string($conditionType)) {
                $conditionType = JoinConditionType::tryFrom($criteria['condition_type']);
            }
        }

        $this->applyJoin(
            $queryBuilder,
            $fieldAlias . '.' . $fieldName,
            $joinAlias,
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
        string $joinAlias,
        mixed $conditions
    ): ?string {
        if (is_string($conditions)) {
            return $conditions;
        }

        if ($conditions instanceof Base) {
            return (string) $conditions;
        }

        $condition = null;
        if (is_array($conditions)) {
            $tempQueryBuilder = $this->filterJoinCriteria(
                $queryBuilder->createQueryBuilder(),
                $targetEntity,
                ['filters' => $this->createJoinFilters($conditions, $joinAlias)]
            );
            $condition = $this->mergeJoinConditions($queryBuilder, $tempQueryBuilder);
        }

        return isset($condition) ? (string) $condition : null;
    }

    /**
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
                    'Failed to apply query filter \'%s\' conditions for target entity \'%s\'',
                    static::class,
                    $targetEntity,
                ),
                $e->getCode(),
                $e
            );
        }

        return $qb;
    }

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

    protected function resolveFieldAlias(QueryBuilderInterface $queryBuilder, string $fieldName): string
    {
        $parts = explode('.', $fieldName);
        return $this->getAlias($queryBuilder, count($parts) > 1 ? $parts[0] : null);
    }
}
