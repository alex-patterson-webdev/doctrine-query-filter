<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Doctrine\ORM\Query\Expr;

final class IsIn extends AbstractExpression
{
    protected function createExpression(Expr $expr, string $fieldName, string $parameterName, string $alias): string
    {
        return (string) $expr->in($alias . '.' . $fieldName, ':' . $parameterName);
    }
}
