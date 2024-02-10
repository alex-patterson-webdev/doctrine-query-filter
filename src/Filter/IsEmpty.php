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
            'name' => OrX::class,
            'conditions' => [
                [
                    'name' => IsNull::class,
                    'field' => $criteria['field'],
                    'value' => null,
                ],
                [
                    'name' => IsEqual::class,
                    'field' => $criteria['field'],
                    'value' => '',
                ],
            ],
        ];

        $this->applyFilter($queryBuilder, $metadata, $criteria);
    }
}
