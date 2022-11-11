<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

class IsBetween extends AbstractFilter
{
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
        if (empty($criteria['from'])) {
            throw new InvalidArgumentException(
                sprintf('The required \'from\' criteria option is missing for filter \'%s\'', self::class)
            );
        }

        if (empty($criteria['to'])) {
            throw new InvalidArgumentException(
                sprintf('The required \'to\' criteria option is missing for filter \'%s\'', self::class)
            );
        }

        $fieldName = $this->resolveFieldName($metadata, $criteria);
        $queryAlias = $this->getAlias($queryBuilder, $criteria['alias'] ?? null);

        $fromParamName = $this->createParamName($queryAlias);
        $toParamName = $this->createParamName($queryAlias);

        $expression = $queryBuilder->expr()->between(
            $queryAlias . '.' . $fieldName,
            ':' . $fromParamName,
            ':' . $toParamName
        );

        if ($this->getWhereType($criteria) === WhereType::AND) {
            $queryBuilder->andWhere($expression);
        } else {
            $queryBuilder->orWhere($expression);
        }

        $format = $criteria['format'] ?? null;

        $queryBuilder->setParameter(
            $fromParamName,
            $this->formatValue($metadata, $fieldName, $criteria['from'], $format)
        );

        $queryBuilder->setParameter(
            $toParamName,
            $this->formatValue($metadata, $fieldName, $criteria['to'], $format)
        );
    }
}
