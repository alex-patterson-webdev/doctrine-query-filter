<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * QueryFilterInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface QueryFilterInterface
{
    /**
     * build
     *
     * Build the query filter expression.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder) : string;
}