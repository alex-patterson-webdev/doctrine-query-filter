<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\Enum\SortDirection;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Sort
 */
final class Field implements SortInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array<mixed>          $data
     *
     * @throws SortException
     * @throws \ReflectionException
     */
    public function sort(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $data): void
    {
        $alias = $data['alias'] ?? $queryBuilder->getRootAlias();

        if (empty($data['field'])) {
            throw new SortException(
                sprintf('The required \'field\' option is missing or empty in \'%s\'', self::class)
            );
        }

        if (empty($data['direction'])) {
            throw new SortException(
                sprintf('The required \'direction\' option is missing or empty in \'%s\'', self::class)
            );
        }

        if (!SortDirection::hasValue($data['direction'])) {
            throw new SortException(
                sprintf(
                    'The sort \'direction\' option value \'%s\' is invalid in \'%s\'',
                    $data['direction'],
                    self::class
                )
            );
        }

        $queryBuilder->addOrderBy($alias . '.' . $data['field'], $data['direction']);
    }
}
