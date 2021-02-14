<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * Default filter manager simply creates a filter using the provided $name as the FQCN of the target filter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var array|string[]
     */
    private array $classMap = [
        'eq'        => IsEqual::class,
        'neq'       => IsNotEqual::class,
        'gt'        => IsGreaterThan::class,
        'gte'       => IsGreaterThanOrEqual::class,
        'lt'        => IsLessThan::class,
        'lte'       => IsLessThanOrEqual::class,
        'isnull'    => IsNull::class,
        'memberof'  => IsMemberOf::class,
        'between'   => IsBetween::class,
        'andx'      => AndX::class,
        'orx'       => OrX::class,
        'leftjoin'  => LeftJoin::class,
        'innerjoin' => InnerJoin::class,
    ];

    /**
     * @param array $classMap
     */
    public function __construct(array $classMap = [])
    {
        $this->classMap = empty($classMap) ? $this->classMap : $classMap;
    }

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
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface
    {
        $className = $this->classMap[$name] ?? $name;

        if (!is_a($className, FilterInterface::class, true)) {
            throw new FilterException(
                sprintf('The query filter \'%s\' must be an object which implements \'%s\'',
                    $className,
                    FilterInterface::class,
                )
            );
        }

        try {
            return new $className($manager);
        } catch (\Throwable $e) {
            throw new FilterException(
                sprintf('Failed to create query filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
