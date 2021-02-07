<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
final class IsBetween extends AbstractFilter
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array                 $criteria
     *
     * @throws InvalidArgumentException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        $fieldName = $this->resolveFieldName($metadata, $criteria);

        $queryAlias = $criteria['alias'] ?? 'entity';

        $fromParamName = uniqid($queryAlias, false);
        $toParamName = uniqid($queryAlias, false);

        $expression = $queryBuilder->expr()->between(
            $queryAlias . '.' . $fieldName,
            ':' . $fromParamName,
            ':' . $toParamName
        );

        if (!isset($criteria['where']) || WhereType::AND === $criteria['where']) {
            $queryBuilder->andWhere($expression);
        } else {
            $queryBuilder->orWhere($expression);
        }

        $queryBuilder->setParameter(
            $fromParamName,
            $this->formatValue($metadata, $fieldName, $criteria['from'], $criteria['format'] ?? null)
        );

        $queryBuilder->setParameter(
            $toParamName,
            $this->formatValue($metadata, $fieldName, $criteria['to'], $criteria['format'] ?? null)
        );
    }
}
