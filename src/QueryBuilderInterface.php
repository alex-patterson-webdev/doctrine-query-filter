<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Enum\OrderByDirection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

interface QueryBuilderInterface
{
    public function createQueryBuilder(): QueryBuilderInterface;

    public function getEntityManager(): EntityManagerInterface;

    public function getRootAlias(): string;

    public function setRootAlias(string $rootAlias): void;

    public function getQuery(): Query;

    public function getWrappedQueryBuilder(): DoctrineQueryBuilder;

    public function expr(): Expr;

    public function getQueryParts(): array;

    public function orWhere(mixed ...$args): self;

    public function andWhere(mixed ...$args): self;

    public function innerJoin(
        string $name,
        string $alias,
        ?JoinConditionType $conditionType = null,
        string|Comparison|Composite|null $condition = null,
        ?string $indexBy = null
    ): self;

    public function leftJoin(
        string $name,
        string $alias,
        ?JoinConditionType $conditionType = null,
        string|Comparison|Composite|null $condition = null,
        string $indexBy = null
    ): QueryBuilderInterface;

    public function orderBy(Expr\OrderBy|string $sort, ?OrderByDirection $direction = null): self;

    public function addOrderBy(Expr\OrderBy|string $sort, ?OrderByDirection $direction = null): self;

    /**
     * @return ArrayCollection<int, Query\Parameter>
     */
    public function getParameters(): ArrayCollection;

    public function mergeParameters(QueryBuilderInterface $queryBuilder): self;

    /**
     * @param ArrayCollection<int, Query\Parameter> $parameters
     *
     * @return QueryBuilderInterface
     */
    public function setParameters(ArrayCollection $parameters): self;

    public function setParameter(string $name, mixed $value, ?string $type = null): self;
}
