<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\Enum\OrderByDirection;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortException;

final class Field implements SortInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<string, mixed> $data
     *
     * @throws SortException
     */
    public function sort(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $data): void
    {
        $alias = $data['alias'] ?? $queryBuilder->getRootAlias();

        if (empty($data['field'])) {
            throw new SortException(
                sprintf('The required \'field\' option is missing or empty in \'%s\'', self::class)
            );
        }

        $direction = $data['direction'] ?? null;
        if (is_string($direction)) {
            try {
                $direction = OrderByDirection::from($data['direction']);
            } catch (\Throwable) {
                throw new SortException(
                    sprintf('The sort direction provided for field \'%s\' is invalid', $data['field'])
                );
            }
        }

        $queryBuilder->addOrderBy($alias . '.' . $data['field'], $direction);
    }
}
