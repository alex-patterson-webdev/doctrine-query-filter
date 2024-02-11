<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\EndsWith;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\EndsWith
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class EndsWithTest extends MockeryTestCase
{
    private QueryFilterManagerInterface&MockInterface $queryFilterManager;

    private QueryBuilderInterface&MockInterface $queryBuilder;

    private MetadataInterface&MockInterface $metadata;

    private EndsWith $endsWith;

    public function setUp(): void
    {
        $this->queryFilterManager = \Mockery::mock(QueryFilterManagerInterface::class);
        $this->queryBuilder = \Mockery::mock(QueryBuilderInterface::class);
        $this->metadata = \Mockery::mock(MetadataInterface::class);

        $this->endsWith = new EndsWith(
            $this->queryFilterManager,
            \Mockery::mock(TypecasterInterface::class),
            \Mockery::mock(ParamNameGeneratorInterface::class),
        );
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->endsWith);
    }

    /**
     * @throws FilterException
     */
    public function testMissingValueCriteriaThrowsFilterException(): void
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'value\' option is missing in criteria for filter \'%s\'', EndsWith::class),
        );

        $this->endsWith->filter($this->queryBuilder, $this->metadata, []);
    }

    /**
     * @dataProvider getFilterData
     *
     * @throws FilterException
     */
    public function testFilter(array $criteria): void
    {
        $expectedCriteria = array_merge(
            $criteria,
            ['name' => 'like', 'value' => '%' . $criteria['value']],
        );

        $this->queryFilterManager->shouldReceive('applyFilter')
            ->once()
            ->with($this->queryBuilder, $this->metadata, $expectedCriteria);

        $this->endsWith->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    public static function getFilterData(): array
    {
        return [
            [
                [
                    'field' => 'foo',
                    'value' => 'test',
                ],

                [
                    'field' => 'bar',
                    'value' => 'example',
                    'alias' => 'x',
                ],
            ],
        ];
    }
}
