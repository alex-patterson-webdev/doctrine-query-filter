<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Query
 */
interface QueryBuilderInterface
{
    /**
     * @return Expr
     */
    public function expr(): Expr;

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * @return QueryBuilderInterface
     */
    public function createQueryBuilder(): QueryBuilderInterface;

    /**
     * @return array
     */
    public function getQueryParts(): array;

    /**
     * @return ArrayCollection
     */
    public function getParameters(): ArrayCollection;

    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return QueryBuilderInterface
     */
    public function mergeParameters(QueryBuilderInterface $queryBuilder): QueryBuilderInterface;

    /**
     * @param ArrayCollection $parameters
     *
     * @return QueryBuilderInterface
     */
    public function setParameters(ArrayCollection $parameters): QueryBuilderInterface;

    /**
     * @param string $name
     * @param mixed $value
     * @param string|null $type
     *
     * @return QueryBuilderInterface
     */
    public function setParameter(string $name, $value, ?string $type = null): QueryBuilderInterface;

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
     * @return Query
     */
    public function getQuery(): Query;
}
