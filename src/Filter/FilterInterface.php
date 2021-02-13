<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
interface FilterInterface
{
    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array                 $criteria
     *
     * @throws FilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void;
}
