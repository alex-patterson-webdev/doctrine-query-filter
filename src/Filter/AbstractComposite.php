<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Exception\QueryFilterManagerException;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Composite;

abstract class AbstractComposite extends AbstractFilter
{
    abstract protected function createComposite(QueryBuilderInterface $queryBuilder): Composite;

    /**
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        if (empty($criteria['conditions'])) {
            return;
        }
        $qb = $this->createNewQueryBuilder($queryBuilder);
        $this->applyConditions($qb, $metadata, $criteria['conditions']);

        $parts = $qb->getQueryParts();
        if (
            !isset($parts['where'])
            || (!$parts['where'] instanceof Composite)
            || (!method_exists($parts['where'], 'getParts'))
        ) {
            return;
        }

        $compositeExpr = $this->createComposite($queryBuilder);
        $compositeExpr->addMultiple($parts['where']->getParts());

        if ($this->getWhereType($criteria) === WhereType::AND) {
            $queryBuilder->andWhere($compositeExpr);
        } else {
            $queryBuilder->orWhere($compositeExpr);
        }

        $queryBuilder->mergeParameters($qb);
    }

    protected function createNewQueryBuilder(QueryBuilderInterface $queryBuilder): QueryBuilderInterface
    {
        $qb = $queryBuilder->createQueryBuilder();

        $alias = $queryBuilder->getRootAlias();
        if (!empty($alias)) {
            $qb->setRootAlias($alias);
        }

        return $qb;
    }

    /**
     * @throws FilterException
     */
    private function applyConditions(QueryBuilderInterface $qb, MetadataInterface $metadata, iterable $conditions): void
    {
        try {
            $this->queryFilterManager->filter($qb, $metadata->getName(), ['filters' => $conditions]);
        } catch (QueryFilterManagerException $e) {
            throw new FilterException(
                sprintf('Failed to construct query filter \'%s\' conditions', static::class),
                $e->getCode(),
                $e
            );
        }
    }
}
