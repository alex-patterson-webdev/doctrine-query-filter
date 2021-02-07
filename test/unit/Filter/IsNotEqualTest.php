<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\IsNotEqual;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsNotEqualTest extends TestCase
{
    /**
     * @var QueryFilterManager|MockObject
     */
    private $queryFilterManager;

    /**
     * @var MetadataInterface|MockObject
     */
    private $metadata;

    /**
     * @var QueryBuilderInterface|MockObject
     */
    private $queryBuilder;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManager::class);

        $this->metadata = $this->createMock(MetadataInterface::class);

        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
    }

    /**
     * Assert that IsEqual implement FilterInterface
     */
    public function testImplementsFilterInterface(): void
    {
        $filter = new IsNotEqual($this->queryFilterManager);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredFieldNameCriteriaIsMissing(): void
    {
        $filter = new IsNotEqual($this->queryFilterManager);

        $criteria = [
            // No field 'name' will raise exception
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'field\' criteria value is missing for filter \'%s\'', IsNotEqual::class)
        );

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }
}
