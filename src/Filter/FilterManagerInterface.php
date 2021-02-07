<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
interface FilterManagerInterface
{
    /**
     * Create the $name query filter with the provided $options.
     *
     * @param QueryFilterManagerInterface $manager
     * @param string                      $name
     * @param array                       $options
     *
     * @return FilterInterface
     *
     * @throws QueryFilterException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface;
}
