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
interface QueryBuilderInterface
{
    /**
     * @return QueryBuilderInterface
     */
    public function createQueryBuilder(): QueryBuilderInterface;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface;

    /**
     * @return string
     */
    public function getRootAlias(): string;

    /**
     * @param string $rootAlias
     */
    public function setRootAlias(string $rootAlias): void;

    /**
     * @return Query
     */
    public function getQuery(): Query;

    /**
     * Return the wrapped Doctrine query builder instance
     *
     * @return DoctrineQueryBuilder
     */
    public function getWrappedQueryBuilder(): DoctrineQueryBuilder;

    /**
     * @return Expr
     */
    public function expr(): Expr;

    /**
     * @return array<mixed>
     */
    public function getQueryParts(): array;

    /**
     * @param mixed ...$args
     *
     * @return QueryBuilderInterface
     */
    public function orWhere(...$args): QueryBuilderInterface;

    /**
     * @param mixed ...$args
     *
     * @return QueryBuilderInterface
     */
    public function andWhere(...$args): QueryBuilderInterface;

    /**
     * @param string      $name
     * @param string      $alias
     * @param string      $type
     * @param mixed       $condition
     * @param string|null $indexBy
     *
     * @return QueryBuilderInterface
     */
    public function innerJoin(
        string $name,
        string $alias,
        string $type,
        $condition = null,
        string $indexBy = null
    ): QueryBuilderInterface;

    /**
     * @param string      $name
     * @param string      $alias
     * @param string      $type
     * @param mixed       $condition
     * @param string|null $indexBy
     *
     * @return QueryBuilderInterface
     */
    public function leftJoin(
        string $name,
        string $alias,
        string $type,
        $condition = null,
        string $indexBy = null
    ): QueryBuilderInterface;

    /**
     * @param Expr\OrderBy|string $sort
     * @param string|null         $direction
     *
     * @return QueryBuilderInterface
     */
    public function orderBy($sort, ?string $direction = null): QueryBuilderInterface;

    /**
     * @param Expr\OrderBy|string $sort
     * @param string|null         $direction
     *
     * @return QueryBuilderInterface
     */
    public function addOrderBy($sort, ?string $direction = null): QueryBuilderInterface;

    /**
     * @return ArrayCollection<int, Query\Parameter>
     */
    public function getParameters(): ArrayCollection;

    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return QueryBuilderInterface
     */
    public function mergeParameters(QueryBuilderInterface $queryBuilder): QueryBuilderInterface;

    /**
     * @param ArrayCollection<int, Query\Parameter> $parameters
     *
     * @return QueryBuilderInterface
     */
    public function setParameters(ArrayCollection $parameters): QueryBuilderInterface;

    /**
     * @param string      $name
     * @param mixed       $value
     * @param string|null $type
     *
     * @return QueryBuilderInterface
     */
    public function setParameter(string $name, $value, ?string $type = null): QueryBuilderInterface;
}
