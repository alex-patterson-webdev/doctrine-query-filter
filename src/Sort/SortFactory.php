<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortFactoryException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Sort
 */
final class SortFactory implements SortFactoryInterface
{
    /**
     * @var array<string, string>
     */
    private array $classMap = [
        'field' => Field::class,
    ];

    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * @param array<string, string> $classMap
     * @param array<string, mixed>  $options
     */
    public function __construct(array $classMap = [], array $options = [])
    {
        $this->classMap = empty($classMap) ? $this->classMap : $classMap;
        $this->options = $options;
    }

    /**
     * Create the $name query sort with the provided $options.
     *
     * @param QueryFilterManagerInterface $manager
     * @param string                      $name
     * @param array<mixed>                $options
     *
     * @return SortInterface
     *
     * @throws SortFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): SortInterface
    {
        $className = $this->classMap[$name] ?? $name;

        if (!class_exists($className, true) || !is_a($className, SortInterface::class, true)) {
            throw new SortFactoryException(
                sprintf(
                    'The sort filter \'%s\' must be an object of type \'%s\'; '
                    . 'The resolved class \'%s\' is invalid or cannot be found',
                    $name,
                    SortInterface::class,
                    $className
                )
            );
        }

        $options = array_replace_recursive(
            $this->options['default_sort_options'] ?? [],
            $options
        );

        try {
            return new $className($manager, $options);
        } catch (\Exception $e) {
            throw new SortFactoryException(
                sprintf('Failed to create sort filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
