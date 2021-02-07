<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Composite;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
final class OrX extends AbstractComposite
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return Composite
     */
    protected function createComposite(QueryBuilderInterface $queryBuilder): Composite
    {
        return $queryBuilder->expr()->orX();
    }
}
