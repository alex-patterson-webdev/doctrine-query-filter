<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

/**
 * A service which will perform filtering
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface FilterServiceInterface
{
    /**
     * @param QueryFilterManagerInterface $filterManager
     * @param array<mixed>                $criteria
     *
     * @return iterable<mixed>
     *
     * @throws \Exception
     */
    public function filter(QueryFilterManagerInterface $filterManager, array $criteria): iterable;
}
