<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * Default filter manager simply creates a filter using the provided $name as the FQCN of the target filter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
final class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var TypecasterInterface
     */
    private TypecasterInterface $typecaster;

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
        'null'      => IsNull::class,
        'notnull'   => IsNotNull::class,
        'memberof'  => IsMemberOf::class,
        'between'   => IsBetween::class,
        'andx'      => AndX::class,
        'orx'       => OrX::class,
        'leftjoin'  => LeftJoin::class,
        'innerjoin' => InnerJoin::class,
        'like'      => IsLike::class,
        'notlike'   => IsNotLike::class,
        'in'        => IsIn::class,
        'notin'     => IsNotIn::class,
    ];

    /**
     * @var array<mixed>
     */
    private array $options;

    /**
     * @param TypecasterInterface|null $typecaster
     * @param array<mixed>             $classMap
     * @param array<mixed>             $options
     */
    public function __construct(?TypecasterInterface $typecaster = null, array $classMap = [], array $options = [])
    {
        $this->typecaster = $typecaster ?? new Typecaster();
        $this->classMap = empty($classMap) ? $this->classMap : $classMap;
        $this->options = $options;
    }

    /**
     * Create the $name query filter with the provided $options.
     *
     * @param QueryFilterManagerInterface $manager
     * @param string                      $name
     * @param array<mixed>                $options
     *
     * @return FilterInterface
     *
     * @throws FilterFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface
    {
        $className = $this->classMap[$name] ?? $name;

        if (!class_exists($className, true) || !is_a($className, FilterInterface::class, true)) {
            throw new FilterFactoryException(
                sprintf(
                    'The query filter \'%s\' must be an object of type \'%s\'; '
                    . 'The resolved class \'%s\' is invalid or cannot be found',
                    $name,
                    FilterInterface::class,
                    $className
                )
            );
        }

        $options = array_replace_recursive(
            $this->options['default_filter_options'] ?? [],
            $options
        );

        try {
            return new $className($manager, $this->typecaster, $options);
        } catch (\Throwable $e) {
            throw new FilterFactoryException(
                sprintf('Failed to create query filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @return array|string[]
     */
    public function getClassMap(): array
    {
        return $this->classMap;
    }

    /**
     * @param array<mixed> $classMap
     */
    public function setClassMap(array $classMap): void
    {
        $this->classMap = $classMap;
    }

    /**
     * @param string $alias
     * @param string $className
     */
    public function addToClassMap(string $alias, string $className): void
    {
        $this->classMap[$alias] = $className;
    }
}
