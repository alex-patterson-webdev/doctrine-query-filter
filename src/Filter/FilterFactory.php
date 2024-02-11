<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\Typecaster;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\UniqidParamNameGenerator;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

final class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var array<string, class-string<FilterInterface>>
     */
    private array $classMap = [
        'eq' => IsEqual::class,
        'neq' => IsNotEqual::class,
        'gt' => IsGreaterThan::class,
        'gte' => IsGreaterThanOrEqual::class,
        'lt' => IsLessThan::class,
        'lte' => IsLessThanOrEqual::class,
        'is_null' => IsNull::class,
        'not_null' => IsNotNull::class,
        'member_of' => IsMemberOf::class,
        'between' => IsBetween::class,
        'and' => AndX::class,
        'or' => OrX::class,
        'left_join' => LeftJoin::class,
        'inner_join' => InnerJoin::class,
        'like' => IsLike::class,
        'not_like' => IsNotLike::class,
        'in' => IsIn::class,
        'not_in' => IsNotIn::class,
        'begins_with' => BeginsWith::class,
        'ends_with' => EndsWith::class,
        'empty' => IsEmpty::class,
    ];

    /**
     * @param array<string, class-string<FilterInterface>> $classMap
     */
    public function __construct(
        private ?TypecasterInterface $typecaster = null,
        private ?ParamNameGeneratorInterface $paramNameGenerator = null,
        array $classMap = [],
        private readonly array $options = []
    ) {
        $this->typecaster = $typecaster ?? new Typecaster();
        $this->paramNameGenerator = $this->paramNameGenerator ?? new UniqidParamNameGenerator();
        $this->classMap = empty($classMap) ? $this->classMap : $classMap;
    }

    /**
     * Create the $name query filter with the provided $options.
     *
     * @throws FilterFactoryException
     */
    public function create(QueryFilterManagerInterface $manager, string $name, array $options = []): FilterInterface
    {
        $className = $this->classMap[$name] ?? $name;

        if (!class_exists($className) || !is_a($className, FilterInterface::class, true)) {
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
            /** @throws \Exception */
            return new $className($manager, $this->typecaster, $this->paramNameGenerator, $options);
        } catch (\Exception $e) {
            throw new FilterFactoryException(
                sprintf('Failed to create query filter \'%s\': %s', $name, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function setClassMap(array $classMap): void
    {
        $this->classMap = $classMap;
    }

    /**
     * @param class-string<FilterInterface> $className
     */
    public function addToClassMap(string $alias, string $className): void
    {
        $this->classMap[$alias] = $className;
    }
}
