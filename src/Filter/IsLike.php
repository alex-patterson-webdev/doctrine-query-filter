<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Doctrine\ORM\Query\Expr;

final class IsLike extends AbstractExpression
{
    protected function createExpression(Expr $expr, string $fieldName, string $parameterName, string $alias): string
    {
        return (string) $expr->like($alias . '.' . $fieldName, ':' . $parameterName);
    }
}
