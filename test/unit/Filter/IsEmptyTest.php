<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\IsEmpty;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsEmpty
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsEmptyTest extends MockeryTestCase
{
    private QueryFilterManagerInterface&MockInterface $queryFilterManager;

    private QueryBuilderInterface&MockInterface $queryBuilder;

    private MetadataInterface&MockInterface $metadata;

    private IsEmpty $isEmpty;

    public function setUp(): void
    {
        $this->queryFilterManager = \Mockery::mock(QueryFilterManagerInterface::class);
        $this->queryBuilder = \Mockery::mock(QueryBuilderInterface::class);
        $this->metadata = \Mockery::mock(MetadataInterface::class);

        $this->isEmpty = new IsEmpty(
            $this->queryFilterManager,
            \Mockery::mock(TypecasterInterface::class),
            \Mockery::mock(ParamNameGeneratorInterface::class),
        );
    }

    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->isEmpty);
    }

    /**
     * @throws FilterException
     */
    public function testMissingFieldCriteriaThrowsFilterException(): void
    {
        $this->expectException(FilterException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'field\' option is missing in criteria for filter \'%s\'', IsEmpty::class),
        );

        $this->isEmpty->filter($this->queryBuilder, $this->metadata, []);
    }

    /**
     * @dataProvider getFilterData
     *
     * @throws FilterException
     */
    public function testFilter(array $criteria): void
    {
        $expectedCriteria = [
            'name' => 'or',
            'conditions' => [
                [
                    'name' => 'is_null',
                    'field' => $criteria['field'],
                    'alias' => $criteria['alias'] ?? null,
                ],
                [
                    'name' => 'eq',
                    'field' => $criteria['field'],
                    'alias' => $criteria['alias'] ?? null,
                    'value' => '',
                ],
            ],
        ];

        $this->queryFilterManager->shouldReceive('applyFilter')
            ->once()
            ->with($this->queryBuilder, $this->metadata, $expectedCriteria);

        $this->isEmpty->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    public static function getFilterData(): array
    {
        return [
            [
                [
                    'field' => 'foo',
                ],

                [
                    'field' => 'bar',
                    'alias' => 'x',
                ],
            ],
        ];
    }
}
