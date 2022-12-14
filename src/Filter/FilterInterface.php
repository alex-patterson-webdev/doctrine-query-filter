<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

interface FilterInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed> $criteria
     *
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void;
}
