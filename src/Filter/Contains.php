<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

final class Contains extends AbstractFilter
{
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        if (empty($criteria['value'])) {
            throw new FilterException(
                sprintf('The required \'value\' option is missing in criteria for filter \'%s\'', self::class),
            );
        }

        $this->applyFilter(
            $queryBuilder,
            $metadata,
            array_merge($criteria, ['name' => IsLike::class, 'value' => '%' . $criteria['value'] . '%']),
        );
    }
}
