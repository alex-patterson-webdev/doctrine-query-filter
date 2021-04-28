<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Query
 */
final class QueryBuilder implements QueryBuilderInterface
{
    /**
     * @var DoctrineQueryBuilder
     */
    private DoctrineQueryBuilder $queryBuilder;

    /**
     * @param DoctrineQueryBuilder $queryBuilder
     */
    public function __construct(DoctrineQueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilderInterface
     */
    public function createQueryBuilder(): QueryBuilderInterface
    {
        return new self($this->getEntityManager()->createQueryBuilder());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->queryBuilder->getEntityManager();
    }

    /**
     * @return string
     */
    public function getRootAlias(): string
    {
        return $this->queryBuilder->getRootAliases()[0] ?? '';
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->queryBuilder->getQuery();
    }

    /**
     * Return the wrapped Doctrine query builder instance
     *
     * @return DoctrineQueryBuilder
     */
    public function getWrappedQueryBuilder(): DoctrineQueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * @return Expr
     */
    public function expr(): Expr
    {
        return $this->queryBuilder->expr();
    }

    /**
     * @return  array<mixed>
     */
    public function getQueryParts(): array
    {
        return $this->queryBuilder->getDQLParts();
    }

    /**
     * @param mixed ...$args
     *
     * @return $this
     */
    public function orWhere(...$args): QueryBuilderInterface
    {
        $this->queryBuilder->orWhere(...$args);

        return $this;
    }

    /**
     * @param mixed ...$args
     *
     * @return $this
     */
    public function andWhere(...$args): QueryBuilderInterface
    {
        $this->queryBuilder->andWhere(...$args);

        return $this;
    }

    /**
     * @param string      $name
     * @param string      $alias
     * @param string      $type
     * @param string|null $condition
     * @param string|null $indexBy
     *
     * @return $this
     */
    public function innerJoin(
        string $name,
        string $alias,
        string $type,
        $condition = null,
        string $indexBy = null
    ): QueryBuilderInterface {
        $this->queryBuilder->innerJoin($name, $alias, $type, $condition, $indexBy);

        return $this;
    }

    /**
     * @param string      $name
     * @param string      $alias
     * @param string      $type
     * @param string|null $condition
     * @param string|null $indexBy
     *
     * @return $this
     */
    public function leftJoin(
        string $name,
        string $alias,
        string $type,
        $condition = null,
        string $indexBy = null
    ): QueryBuilderInterface {
        $this->queryBuilder->leftJoin($name, $alias, $type, $condition, $indexBy);

        return $this;
    }

    /**
     * @return ArrayCollection<mixed, Query\Parameter>
     */
    public function getParameters(): ArrayCollection
    {
        return $this->queryBuilder->getParameters();
    }

    /**
     * @param ArrayCollection<mixed, Query\Parameter> $parameters
     *
     * @return $this|QueryBuilderInterface
     */
    public function setParameters(ArrayCollection $parameters): QueryBuilderInterface
    {
        $this->queryBuilder->setParameters($parameters);

        return $this;
    }

    /**
     * @param string      $name
     * @param mixed       $value
     * @param string|null $type
     *
     * @return $this|QueryBuilderInterface
     */
    public function setParameter(string $name, $value, ?string $type = null): QueryBuilderInterface
    {
        $this->queryBuilder->setParameter($name, $value, $type);

        return $this;
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return $this|QueryBuilderInterface
     */
    public function mergeParameters(QueryBuilderInterface $queryBuilder): QueryBuilderInterface
    {
        $parameters = $this->getParameters();
        foreach ($queryBuilder->getParameters() as $parameter) {
            $parameters->add($parameter);
        }

        return $this;
    }
}
