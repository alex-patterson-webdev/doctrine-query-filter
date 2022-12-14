<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Composite;

final class AndX extends AbstractComposite
{
    protected function createComposite(QueryBuilderInterface $queryBuilder): Composite
    {
        return $queryBuilder->expr()->andX();
    }
}
