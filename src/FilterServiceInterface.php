<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

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
