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
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

final class QueryBuilder implements QueryBuilderInterface
{
    public function __construct(
        private readonly DoctrineQueryBuilder $queryBuilder,
        private string $rootAlias = ''
    ) {
    }

    public function createQueryBuilder(): QueryBuilderInterface
    {
        return new self($this->getEntityManager()->createQueryBuilder());
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->queryBuilder->getEntityManager();
    }

    public function getRootAlias(): string
    {
        if (!empty($this->rootAlias)) {
            return $this->rootAlias;
        }

        return $this->queryBuilder->getRootAliases()[0] ?? '';
    }

    public function setRootAlias(string $rootAlias): void
    {
        $this->rootAlias = $rootAlias;
    }

    public function getQuery(): Query
    {
        return $this->queryBuilder->getQuery();
    }

    public function getWrappedQueryBuilder(): DoctrineQueryBuilder
    {
        return $this->queryBuilder;
    }

    public function expr(): Expr
    {
        return $this->queryBuilder->expr();
    }

    /**
     * @return array<string, mixed>
     */
    public function getQueryParts(): array
    {
        return $this->queryBuilder->getDQLParts();
    }

    public function orWhere(mixed ...$args): QueryBuilderInterface
    {
        $this->queryBuilder->orWhere(...$args);

        return $this;
    }

    public function andWhere(mixed ...$args): QueryBuilderInterface
    {
        $this->queryBuilder->andWhere(...$args);

        return $this;
    }

    /**
     * @param string $name
     * @param string $alias
     * @param JoinConditionType|null $conditionType
     * @param string|Comparison|Composite|null $condition
     * @param string|null $indexBy
     *
     * @return self
     */
    public function innerJoin(
        string $name,
        string $alias,
        ?JoinConditionType $conditionType = null,
        mixed $condition = null,
        ?string $indexBy = null
    ): self {
        $conditionType ??= JoinConditionType::WITH;
        $this->queryBuilder->innerJoin($name, $alias, $conditionType->value, $condition, $indexBy);

        return $this;
    }

    /**
     * @param string $name
     * @param string $alias
     * @param JoinConditionType|null $conditionType
     * @param string|Comparison|Composite|null $condition
     * @param string|null $indexBy
     *
     * @return self
     */
    public function leftJoin(
        string $name,
        string $alias,
        ?JoinConditionType $conditionType = null,
        mixed $condition = null,
        ?string $indexBy = null
    ): self {
        $conditionType ??= JoinConditionType::WITH;
        $this->queryBuilder->leftJoin($name, $alias, $conditionType->value, $condition, $indexBy);

        return $this;
    }

    public function orderBy(Expr\OrderBy|string $sort, ?OrderByDirection $direction = null): self
    {
        if ($sort instanceof Expr\OrderBy) {
            $direction = null;
        }

        $this->queryBuilder->orderBy($sort, $direction?->value);

        return $this;
    }

    public function addOrderBy(OrderBy|string $sort, ?OrderByDirection $direction = null): self
    {
        if ($sort instanceof Expr\OrderBy) {
            $direction = null;
        }

        $this->queryBuilder->addOrderBy($sort, $direction?->value);

        return $this;
    }

    /**
     * @return ArrayCollection<int, Query\Parameter>
     */
    public function getParameters(): ArrayCollection
    {
        return $this->queryBuilder->getParameters();
    }

    public function setParameters(ArrayCollection $parameters): self
    {
        $this->queryBuilder->setParameters($parameters);

        return $this;
    }

    public function setParameter(string $name, mixed $value, ?string $type = null): self
    {
        $this->queryBuilder->setParameter($name, $value, $type);

        return $this;
    }

    public function mergeParameters(QueryBuilderInterface $queryBuilder): self
    {
        $parameters = $this->getParameters();
        foreach ($queryBuilder->getParameters() as $parameter) {
            $parameters->add($parameter);
        }

        return $this;
    }
}
