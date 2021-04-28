<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\AbstractFilter;
use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class ThrowExceptionInConstructorFilterMock extends AbstractFilter
{
    /**
     * @param QueryFilterManagerInterface $queryFilterManager
     * @param TypecasterInterface         $typecaster
     * @param array<mixed>                $options
     */
    public function __construct(
        QueryFilterManagerInterface $queryFilterManager,
        TypecasterInterface $typecaster,
        array $options = []
    ) {
        parent::__construct($queryFilterManager, $typecaster, $options);

        throw new \RuntimeException('This is is a test exception');
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array<mixed>          $criteria
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
    }
}
