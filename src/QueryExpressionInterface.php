<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;

/**
 * QueryExpressionInterface
 *
 * A section of SQL.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface QueryExpressionInterface
{
    /**
     * build
     *
     * Construct a DQL 'expression' string.
     *
     * @param QueryExpressionFactoryInterface $factory
     *
     * @return string
     */
    public function build(QueryExpressionFactoryInterface $factory) : string;
}