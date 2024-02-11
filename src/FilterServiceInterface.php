<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter;

interface FilterServiceInterface
{
    /**
     * @throws \Exception
     */
    public function filter(QueryFilterManagerInterface $filterManager, array $criteria): iterable;
}
