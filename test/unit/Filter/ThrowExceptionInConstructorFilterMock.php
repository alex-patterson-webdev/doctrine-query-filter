<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\AbstractFilter;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;

final class ThrowExceptionInConstructorFilterMock extends AbstractFilter
{
    /**
     * @param QueryFilterManagerInterface $queryFilterManager
     * @param TypecasterInterface $typecaster
     * @param ParamNameGeneratorInterface $paramNameGenerator
     * @param array<mixed> $options
     */
    public function __construct(
        QueryFilterManagerInterface $queryFilterManager,
        TypecasterInterface $typecaster,
        ParamNameGeneratorInterface $paramNameGenerator,
        array $options = []
    ) {
        parent::__construct($queryFilterManager, $typecaster, $paramNameGenerator, $options);

        throw new \RuntimeException('This is is a test exception');
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface $metadata
     * @param array<mixed> $criteria
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {
    }
}
