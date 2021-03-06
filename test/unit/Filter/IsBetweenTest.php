<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\IsBetween;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers  \Arp\DoctrineQueryFilter\Filter\IsBetween
 * @covers  \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsBetweenTest extends AbstractFilterTest
{
    /**
     * Assert that the class implements FilterInterface
     */
    public function testImplementsFilterInterface(): void
    {
        $filter = new IsBetween($this->queryFilterManager, $this->typecaster);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * Assert that a InvalidArgumentException is thrown when attempting to call filter() without
     * the required 'from' key
     *
     * @throws FilterException
     * @throws InvalidArgumentException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredFromCriteriaIsMissing(): void
    {
        $filter = new IsBetween($this->queryFilterManager, $this->typecaster);

        $criteria = [
            // Missing 'from' key
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'from\' criteria option is missing for filter \'%s\'', IsBetween::class)
        );

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * Assert that a InvalidArgumentException is thrown when attempting to call filter() without
     * the required 'to' key
     *
     * @throws FilterException
     * @throws InvalidArgumentException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredToCriteriaIsMissing(): void
    {
        $filter = new IsBetween($this->queryFilterManager, $this->typecaster);

        $criteria = [
            'from' => '2021-03-01 00:00:00',
            // Missing required 'to' key
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('The required \'to\' criteria option is missing for filter \'%s\'', IsBetween::class)
        );

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * @param array $criteria
     *
     * @throws FilterException
     * @throws InvalidArgumentException
     *
     * @dataProvider getFilterIsBetweenData
     */
    public function testFilterIsBetween(array $criteria): void
    {
        /** @var IsBetween|MockObject $filter */
        $filter = $this->getMockBuilder(IsBetween::class)
            ->setConstructorArgs([$this->queryFilterManager, $this->typecaster])
            ->onlyMethods(['createParamName'])
            ->getMock();

        $rootAlias = 'entity';
        $from = $criteria['from'] ?? '';
        $to = $criteria['to'] ?? '';
        $alias = $criteria['alias'] ?? 'entity';
        $fieldName = $criteria['field'] = $criteria['field'] ?? 'test';
        $formatType = $criteria['format'] ?? null;

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        if (empty($criteria['alias'])) {
            $alias = $rootAlias;

            $this->queryBuilder->expects($this->once())
                ->method('getRootAlias')
                ->willReturn($rootAlias);
        }

        /** @var Expr|MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        $fromParam = $alias . 'abc123';
        $toParam = $alias . 'zyx999';

        $filter->expects($this->exactly(2))
            ->method('createParamName')
            ->withConsecutive(
                [$alias],
                [$alias]
            )->willReturnOnConsecutiveCalls(
                $fromParam,
                $toParam
            );

        $isBetween = $alias . '.' . $fieldName . ' BETWEEN ' . $fromParam . ' AND ' . $toParam;
        $expr->expects($this->once())
            ->method('between')
            ->with(
                $alias . '.' . $fieldName,
                ':' . $fromParam,
                ':' . $toParam
            )->willReturn(
                $isBetween
            );

        if (empty($criteria['where']) || WhereType:: AND === $criteria['where']) {
            $this->queryBuilder->expects($this->once())
                ->method('andWhere')
                ->with($isBetween);
        } else {
            $this->queryBuilder->expects($this->once())
                ->method('orWhere')
                ->with($isBetween);
        }

        $this->typecaster->expects($this->exactly(2))
            ->method('typecast')
            ->withConsecutive(
                [$this->metadata, $fieldName, $from, $formatType, []],
                [$this->metadata, $fieldName, $to, $formatType, []],
            )->willReturnOnConsecutiveCalls(
                $from,
                $to
            );

        $this->queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                [$fromParam, $from],
                [$toParam, $to]
            );

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * @return array
     */
    public function getFilterIsBetweenData(): array
    {
        return [
            [
                [
                    'to'   => '2021-01-01 00:00:00',
                    'from' => '2021-02-01 00:00:00',
                ],
            ],

            [
                [
                    'to'    => '2021-01-01 00:00:00',
                    'from'  => '2021-02-01 00:00:00',
                    'where' => WhereType:: AND,
                ],
            ],

            [
                [
                    'to'    => '2021-01-01 00:00:00',
                    'from'  => '2021-02-01 00:00:00',
                    'where' => WhereType:: OR,
                ],
            ],

            [
                [
                    'to'    => '2000-01-01 11:12:45',
                    'from'  => '2021-01-01 07:35:17',
                    'alias' => 'test_alias_123',
                ],
            ],
        ];
    }
}
