<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
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
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        if (empty($criteria['from'])) {
            throw new InvalidArgumentException(
                sprintf('The required \'from\' criteria option is missing for filter \'%s\'', static::class)
            );
        }

        if (empty($criteria['to'])) {
            throw new InvalidArgumentException(
                sprintf('The required \'to\' criteria option is missing for filter \'%s\'', static::class)
            );
        }

        $fieldName = $this->resolveFieldName($metadata, $criteria);

        $queryAlias = (($criteria['alias'] ?? $this->options['alias']) ?? 'entity');

        $fromParamName = $this->createParamName($queryAlias);
        $toParamName = $this->createParamName($queryAlias);

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
