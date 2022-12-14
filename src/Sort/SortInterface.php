<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortException;

interface SortInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed> $data
     *
     * @throws SortException
     */
    public function sort(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $data): void;
}
