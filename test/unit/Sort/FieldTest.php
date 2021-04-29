<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Sort;

use Arp\DoctrineQueryFilter\Enum\SortDirection;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Sort\Exception\SortException;
use Arp\DoctrineQueryFilter\Sort\Field;
use Arp\DoctrineQueryFilter\Sort\SortInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Sort
 */
final class FieldTest extends TestCase
{
    /**
     * @var QueryBuilderInterface&MockObject
     */
    private $queryBuilder;

    /**
     * @var MetadataInterface&MockObject
     */
    private $metadata;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->metadata = $this->createMock(MetadataInterface::class);
    }

    /**
     * Assert the class implements SortInterface
     */
    public function testImplementsSortInterface(): void
    {
        $sort = new Field();

        $this->assertInstanceOf(SortInterface::class, $sort);
    }

    /**
     * Assert a SortException is thrown from sort() if the required 'field' parameter is missing/empty
     *
     * @throws SortException
     * @throws \ReflectionException
     */
    public function testSortWillThrowSortExceptionIfRequiredFieldParameterIsMissing(): void
    {
        $sort = new Field();

        $alias = 't';
        $data = [];

        $this->queryBuilder->expects($this->once())
            ->method('getRootAlias')
            ->willReturn($alias);

        $this->expectException(SortException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'field\' option is missing or empty in \'%s\'', Field::class)
        );

        $sort->sort($this->queryBuilder, $this->metadata, $data);
    }

    /**
     * Assert a SortException is thrown from sort() if the required 'direction' parameter is missing/empty
     *
     * @throws SortException
     * @throws \ReflectionException
     */
    public function testSortWillThrowSortExceptionIfRequiredDirectionParameterIsMissing(): void
    {
        $sort = new Field();

        $alias = 't';
        $data = [
            'field' => 'foo',
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getRootAlias')
            ->willReturn($alias);

        $this->expectException(SortException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'direction\' option is missing or empty in \'%s\'', Field::class)
        );

        $sort->sort($this->queryBuilder, $this->metadata, $data);
    }

    /**
     * Assert that a SortException is thrown should the provided $direction be invalid
     *
     * @throws SortException
     * @throws \ReflectionException
     */
    public function testSortWillThrowSortExceptionIfTheDirectionParameterIsInvalid(): void
    {
        $sort = new Field();

        $alias = 't';
        $data = [
            'field' => 'foo',
            'direction' => 'test', // invalid direction value
        ];

        $this->queryBuilder->expects($this->once())
            ->method('getRootAlias')
            ->willReturn($alias);

        $this->expectException(SortException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The sort \'direction\' option value \'%s\' is invalid in \'%s\'',
                $data['direction'],
                Field::class
            )
        );

        $sort->sort($this->queryBuilder, $this->metadata, $data);
    }

    /**
     * Assert that the expected sort parameters are correct passed to the QueryBuilder
     *
     * @param string      $field
     * @param string      $direction
     * @param string|null $alias
     *
     * @dataProvider getSortWillApplySortCriteriaData
     *
     * @throws SortException
     * @throws \ReflectionException
     */
    public function testSortWillApplySortCriteria(string $field, string $direction, ?string $alias = null): void
    {
        $sort = new Field();

        $rootAlias = 'x';
        $data = [
            'field' => $field,
            'direction' => $direction,
        ];

        if (null === $alias) {
            $alias = $rootAlias;
            $this->queryBuilder->expects($this->once())
                ->method('getRootAlias')
                ->willReturn($rootAlias);
        } else {
            $data['alias'] = $alias;
        }

        $this->queryBuilder->expects($this->once())
            ->method('addOrderBy')
            ->with($alias . '.' . $field, $direction);

        $sort->sort($this->queryBuilder, $this->metadata, $data);
    }

    /**
     * @return array[]
     */
    public function getSortWillApplySortCriteriaData(): array
    {
        return [
            [
                'foo',
                SortDirection::ASC,
            ],
            [
                'bar',
                SortDirection::DESC,
            ],
            [
                'baz',
                SortDirection::DESC,
                'abc',
            ]
        ];
    }
}
