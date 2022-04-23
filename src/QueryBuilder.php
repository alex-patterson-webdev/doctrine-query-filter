<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Query
 */
final class QueryBuilder implements QueryBuilderInterface
{
    private DoctrineQueryBuilder $queryBuilder;

    private string $rootAlias = '';

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
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->queryBuilder->getEntityManager();
    }

    /**
     * @return string
     */
    public function getRootAlias(): string
    {
        if (!empty($this->rootAlias)) {
            return $this->rootAlias;
        }

        return $this->queryBuilder->getRootAliases()[0] ?? '';
    }

    /**
     * @param string $rootAlias
     */
    public function setRootAlias(string $rootAlias): void
    {
        $this->rootAlias = $rootAlias;
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
     * @param Expr\OrderBy|string $sort
     * @param string|null         $direction
     *
     * @return QueryBuilderInterface
     */
    public function orderBy($sort, ?string $direction = null): QueryBuilderInterface
    {
        if ($sort instanceof Expr\OrderBy) {
            $direction = null;
        }

        $this->queryBuilder->orderBy($sort, $direction);

        return $this;
    }

    /**
     * @param Expr\OrderBy|string $sort
     * @param string|null         $direction
     *
     * @return QueryBuilderInterface
     */
    public function addOrderBy($sort, ?string $direction = null): QueryBuilderInterface
    {
        if ($sort instanceof Expr\OrderBy) {
            $direction = null;
        }

        $this->queryBuilder->addOrderBy($sort, $direction);

        return $this;
    }

    /**
     * @return ArrayCollection<int, Query\Parameter>
     */
    public function getParameters(): ArrayCollection
    {
        return $this->queryBuilder->getParameters();
    }

    /**
     * @param ArrayCollection<int, Query\Parameter> $parameters
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
