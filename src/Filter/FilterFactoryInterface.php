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
     * @param QueryFilterManagerInterface $manager
     * @param string $name
     * @param array<mixed> $options
     *
     * @return FilterInterface
     *
     * @throws FilterFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface;
}
