<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * Default filter manager simply creates a filter using the provided $name as the FQCN of the target filter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
class FilterManager implements FilterManagerInterface
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
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface
    {
        if (!is_a($name, FilterInterface::class, true)) {
            throw new QueryFilterException(
                sprintf('The query filter \'%s\' must be an object which implements \'%s\'',
                    $name,
                    FilterInterface::class,
                )
            );
        }

        try {
            return new $name($manager);
        } catch (\Throwable $e) {
            throw new QueryFilterException(
                sprintf('Failed to create query filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
