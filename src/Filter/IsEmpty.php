<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

final class IsEmpty extends AbstractFilter
{
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
        if (empty($criteria['field'])) {
            throw new FilterException(
                sprintf('The required \'field\' option is missing in criteria for filter \'%s\'', self::class),
            );
        }

        $criteria = [
            'name' => 'or',
            'conditions' => [
                [
                    'name' => 'is_null',
                    'field' => $criteria['field'],
                    'alias' => $criteria['alias'] ?? null,
                ],
                [
                    'name' => 'eq',
                    'field' => $criteria['field'],
                    'alias' => $criteria['alias'] ?? null,
                    'value' => '',
                ],
            ],
        ];

        $this->applyFilter($queryBuilder, $metadata, $criteria);
    }
}
