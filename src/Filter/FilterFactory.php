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
     * @param TypecasterInterface|null $typecaster
     * @param ParamNameGeneratorInterface|null $paramNameGenerator
     * @param array<string, class-string<FilterInterface>> $classMap
     * @param array<mixed> $options
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
     * @param QueryFilterManagerInterface $manager
     * @param string $name
     * @param array<mixed> $options
     *
     * @return FilterInterface
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

    /**
     * @return array<string>
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
     * @param class-string<FilterInterface> $className
     */
    public function addToClassMap(string $alias, string $className): void
    {
        $this->classMap[$alias] = $className;
    }
}
