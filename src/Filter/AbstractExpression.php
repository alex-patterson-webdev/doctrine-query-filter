<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
abstract class AbstractExpression extends AbstractFilter
{
    /**
     * Each extending class must return the expression it requires
     *
     * @param Expr   $expr
     * @param string $fieldName
     * @param string $parameterName
     * @param string $alias
     *
     * @return string
     */
    abstract protected function createExpression(
        Expr $expr,
        string $fieldName,
        string $parameterName,
        string $alias
    ): string;

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

        $queryAlias = (($criteria['alias'] ?? $this->options['alias']) ?? 'entity');
        $paramName = $this->createParamName($queryAlias);

        $expression = $this->createExpression($queryBuilder->expr(), $fieldName, $paramName, $queryAlias);
        if (!isset($criteria['where']) || WhereType::AND === $criteria['where']) {
            $queryBuilder->andWhere($expression);
        } else {
            $queryBuilder->orWhere($expression);
        }

        // Some comparisons will not require a value to be provided
        if (array_key_exists('value', $criteria)) {
            $value = $this->formatValue($metadata, $fieldName, $criteria['value'], $criteria['format'] ?? null);
            $queryBuilder->setParameter($paramName, $value);
        }
    }
}
