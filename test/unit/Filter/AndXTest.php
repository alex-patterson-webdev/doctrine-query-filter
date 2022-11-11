<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\AndX;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx as DoctrineAndx;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\AndX
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractComposite
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class AndXTest extends AbstractFilterTest
{
    /**
     * Assert that the filter implements FilterInterface
     */
    public function testImplementsFilterInterface(): void
    {
        $criteria = [
            // no filters provided
        ];

        $filter = new AndX($this->queryFilterManager, $this->typecaster);

        $this->queryBuilder->expects($this->never())->method('createQueryBuilder');

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * Assert that the andX filter will correctly apply the required filters
     *
     * @param string|WhereType|null $whereType
     *
     * @dataProvider getFilterWillApplyTheProvidedConditionsData
     *
     * @throws FilterException
     */
    public function testFilterWillApplyTheProvidedConditions(WhereType|string|null $whereType): void
    {
        $criteria = [
            'conditions' => [
                [
                    'name' => 'eq',
                    'field' => 'test',
                    'value' => 1,
                ],
                [
                    'name' => 'meq',
                    'field' => 'hello',
                    'value' => 'test',
                ],
            ],
        ];

        if (isset($whereType)) {
            $criteria['where'] = $whereType;
        }

        $filter = new AndX($this->queryFilterManager, $this->typecaster);

        $className = 'FooEntity';

        /** @var QueryBuilderInterface&MockObject $qb */
        $qb = $this->createMock(QueryBuilderInterface::class);

        $this->queryBuilder->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $this->metadata->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $this->queryFilterManager->expects($this->once())
            ->method('filter')
            ->with($qb, $className, ['filters' => $criteria['conditions']]);

        /** @var DoctrineAndx&MockObject $where */
        $where = $this->createMock(DoctrineAndx::class);

        $queryParts = [
            'where' => $where,
        ];

        $qb->expects($this->once())
            ->method('getQueryParts')
            ->willReturn($queryParts);

        /** @var Expr&MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        /** @var DoctrineAndx&MockObject $doctrineAndX */
        $doctrineAndX = $this->createMock(DoctrineAndx::class);

        $expr->expects($this->once())
            ->method('andX')
            ->willReturn($doctrineAndX);

        /** @var Expr\Comparison[] $whereParts */
        $whereParts = [
            $this->createMock(Expr\Comparison::class),
            $this->createMock(Expr\Comparison::class),
        ];

        $where->expects($this->once())
            ->method('getParts')
            ->willReturn($whereParts);

        $doctrineAndX->expects($this->once())
            ->method('addMultiple')
            ->with($whereParts);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($doctrineAndX);

        $this->queryBuilder->expects($this->once())
            ->method('mergeParameters')
            ->with($qb);

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * @return array<mixed>
     */
    public function getFilterWillApplyTheProvidedConditionsData(): array
    {
        return [
            [null],
            [WhereType::AND],
            [WhereType::AND->value],
        ];
    }
}
