<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Sort\SortInterface;

/**
 * Sort class used to force a creation error
 */
final class SortCreationErrorMock implements SortInterface
{
    /**
     * @throws \Exception
     */
    public function __construct()
    {
        throw new \RuntimeException('Failed to create sort filter');
    }

    public function sort(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $data): void
    {
    }
}
