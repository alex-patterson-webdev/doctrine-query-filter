<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Andx as DoctrineAndX;
use Doctrine\ORM\Query\Expr\Andx as DoctrineOrX;
use Doctrine\ORM\Query\Expr\Composite;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
abstract class AbstractComposite extends AbstractFilter
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return Composite
     */
    abstract protected function createComposite(QueryBuilderInterface $queryBuilder): Composite;

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array                 $criteria
     *
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        if (empty($criteria['conditions'])) {
            return;
        }
        $qb = $queryBuilder->createQueryBuilder();
        $this->applyConditions($qb, $metadata, $criteria['conditions']);

        $parts = $qb->getQueryParts();
        if (
            !isset($parts['where'])
            || (!$parts['where'] instanceof DoctrineAndx || !$parts['where'] instanceof DoctrineOrX)
        ) {
            return;
        }

        $compositeExpr = $this->createComposite($queryBuilder);
        $compositeExpr->addMultiple($parts['where']->getParts());

        if (!isset($criteria['where']) || WhereType::AND === $criteria['where']) {
            $queryBuilder->andWhere($compositeExpr);
        } else {
            $queryBuilder->orWhere($compositeExpr);
        }

        $queryBuilder->mergeParameters($qb);
    }

    /**
     * @param QueryBuilderInterface $qb
     * @param MetadataInterface     $metadata
     * @param                       $conditions
     *
     * @throws FilterException
     */
    private function applyConditions(QueryBuilderInterface $qb, MetadataInterface $metadata, $conditions): void
    {
        try {
            $this->queryFilterManager->filter($qb, $metadata->getName(), ['filters' => $conditions]);
        } catch (QueryFilterManagerException $e) {
            throw new FilterException(
                sprintf('Failed to construct query filter \'%s\' conditions: %s', static::class, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
