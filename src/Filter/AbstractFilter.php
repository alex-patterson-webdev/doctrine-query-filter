<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var QueryFilterManagerInterface
     */
    protected QueryFilterManagerInterface $queryFilterManager;

    /**
     * @var array
     */
    protected array $options = [];

    /**
     * @param QueryFilterManagerInterface $queryFilterManager
     * @param array                       $options
     */
    public function __construct(QueryFilterManagerInterface $queryFilterManager, array $options = [])
    {
        $this->queryFilterManager = $queryFilterManager;
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Create a new unique parameter name
     *
     * @param string $prefix
     *
     * @return string
     */
    protected function createParamName(string $prefix = ''): string
    {
        return uniqid($prefix, false);
    }

    /**
     * @param MetadataInterface $metadata
     * @param array             $criteria
     * @param string            $key
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function resolveFieldName(MetadataInterface $metadata, array $criteria, string $key = 'field'): string
    {
        if (empty($criteria[$key])) {
            throw new InvalidArgumentException(
                sprintf(
                    'The required \'%s\' criteria value is missing for filter \'%s\'',
                    $key,
                    static::class
                )
            );
        }

        if (!$metadata->hasField($criteria[$key]) && !$metadata->hasAssociation($criteria[$key])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to apply query filter \'%s\': '
                    . 'The entity class \'%s\' has no field or association named \'%s\'',
                    static::class,
                    $metadata->getName(),
                    $criteria[$key]
                )
            );
        }

        return $criteria[$key];
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param mixed             $value
     * @param string|null       $format
     *
     * @return mixed
     *
     * @noinspection PhpUnusedParameterInspection
     */
    protected function formatValue(MetadataInterface $metadata, string $fieldName, $value, ?string $format = null)
    {
        return $value;
    }
}
