<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
abstract class AbstractFilterTest extends TestCase
{
    /**
     * @var QueryFilterManagerInterface|MockObject
     */
    protected $queryFilterManager;

    /**
     * @var TypecasterInterface|MockObject
     */
    protected $typecaster;

    /**
     * @var QueryBuilderInterface|MockObject
     */
    protected $queryBuilder;

    /**
     * @var MetadataInterface|MockObject
     */
    protected $metadata;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManagerInterface::class);

        $this->typecaster = $this->createMock(TypecasterInterface::class);

        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->metadata = $this->createMock(MetadataInterface::class);
    }
}
