<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
interface FilterFactoryInterface
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
     * @throws FilterException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface;
}
