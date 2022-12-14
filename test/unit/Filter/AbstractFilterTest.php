<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\UniqidParamNameGenerator;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractFilterTest extends TestCase
{
    /**
     * @var QueryFilterManagerInterface&MockObject
     */
    protected QueryFilterManagerInterface $queryFilterManager;

    /**
     * @var TypecasterInterface&MockObject
     */
    protected TypecasterInterface $typecaster;

    /**
     * @var ParamNameGeneratorInterface
     */
    protected ParamNameGeneratorInterface $paramNameGenerator;

    /**
     * @var QueryBuilderInterface&MockObject
     */
    protected QueryBuilderInterface $queryBuilder;

    /**
     * @var MetadataInterface&MockObject
     */
    protected MetadataInterface $metadata;

    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManagerInterface::class);
        $this->typecaster = $this->createMock(TypecasterInterface::class);
        $this->paramNameGenerator = new UniqidParamNameGenerator();
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);
    }
}
