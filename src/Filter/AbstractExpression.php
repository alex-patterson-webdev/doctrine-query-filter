<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

abstract class AbstractExpression extends AbstractFilter
{
    abstract protected function createExpression(
        Expr $expr,
        string $fieldName,
        string $parameterName,
        string $alias
    ): string;

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
        $paramName = $this->createParamName($queryAlias);

        $expression = $this->createExpression($queryBuilder->expr(), $fieldName, $paramName, $queryAlias);

        if ($this->getWhereType($criteria) === WhereType::AND) {
            $queryBuilder->andWhere($expression);
        } else {
            $queryBuilder->orWhere($expression);
        }

        // Some comparisons will not require a value to be provided
        if (array_key_exists('value', $criteria)) {
            $value = $this->formatValue(
                $metadata,
                $fieldName,
                $criteria['value'],
                $criteria['type'] ?? null,
                [
                    'format' => $criteria['format'] ?? null,
                ]
            );
            $queryBuilder->setParameter($paramName, $value);
        }
    }
}
