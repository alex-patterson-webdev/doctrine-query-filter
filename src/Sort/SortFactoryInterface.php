<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortFactoryException;

interface SortFactoryInterface
{
    /**
     * Create the $name query sort with the provided $options.
     *
     * @param QueryFilterManagerInterface $manager
     * @param string $name
     * @param array<mixed> $options
     *
     * @return SortInterface
     *
     * @throws SortFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): SortInterface;
}
