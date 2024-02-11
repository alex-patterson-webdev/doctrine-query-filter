<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

interface FilterFactoryInterface
{
    /**
     * Create the $name query filter with the provided $options.
     *
     * @throws FilterFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface;
}
