<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

abstract class AbstractFilter implements FilterInterface
{
    /**
     * @param QueryFilterManagerInterface $queryFilterManager
     * @param TypecasterInterface $typecaster
     * @param ParamNameGeneratorInterface $paramNameGenerator
     * @param array<mixed> $options
     */
    public function __construct(
        protected QueryFilterManagerInterface $queryFilterManager,
        protected TypecasterInterface $typecaster,
        protected ParamNameGeneratorInterface $paramNameGenerator,
        protected array $options = []
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    protected function createParamName(string $param, string $fieldName, string $alias): string
    {
        return $this->paramNameGenerator->generateName($param, $fieldName, $alias);
    }

    protected function getAlias(QueryBuilderInterface $queryBuilder, ?string $alias = null): string
    {
        $alias ??= $queryBuilder->getRootAlias();
        if (!empty($alias)) {
            return $alias;
        }

        return $this->options['alias'] ?? 'x';
    }

    /**
     * @param MetadataInterface $metadata
     * @param array<mixed>      $criteria
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function resolveFieldName(MetadataInterface $metadata, array $criteria): string
    {
        if (empty($criteria['field'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The required \'field\' criteria value is missing for filter \'%s\'',
                    static::class
                )
            );
        }

        $parts = explode('.', $criteria['field']);
        $fieldName = array_pop($parts);

        if (!$metadata->hasField($fieldName) && !$metadata->hasAssociation($fieldName)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to apply query filter \'%s\': '
                    . 'The entity class \'%s\' has no field or association named \'%s\'',
                    static::class,
                    $metadata->getName(),
                    $fieldName
                )
            );
        }

        return $fieldName;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param mixed             $value
     * @param string|null       $type
     * @param array<mixed>      $options
     *
     * @return mixed
     *
     * @throws FilterException
     */
    protected function formatValue(
        MetadataInterface $metadata,
        string $fieldName,
        mixed $value,
        ?string $type = null,
        array $options = []
    ): mixed {
        try {
            return $this->typecaster->typecast($metadata, $fieldName, $value, $type, $options);
        } catch (TypecastException $e) {
            throw new FilterException(
                sprintf('Failed to format the value for field \'%s::%s\'', $metadata->getName(), $fieldName),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return WhereType
     */
    protected function getWhereType(array $criteria): WhereType
    {
        if (isset($criteria['where'])) {
            if (is_string($criteria['where'])) {
                $criteria['where'] = WhereType::tryFrom($criteria['where']);
            }

            if ($criteria['where'] instanceof WhereType) {
                return $criteria['where'];
            }
        }

        return WhereType::AND;
    }
}
