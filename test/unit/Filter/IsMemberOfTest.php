<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\IsMemberOf;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\IsMemberOf
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractExpression
 * @covers \Arp\DoctrineQueryFilter\Filter\AbstractFilter
 */
final class IsMemberOfTest extends AbstractComparisonTest
{
    protected string $filterClassName = IsMemberOf::class;

    protected string $expressionMethodName = 'isMemberOf';

    protected string $expressionSymbol = 'MEMBER OF';

    /**
     * Assert that the IsMemberOf query filter can be applied with the provided $criteria
     *
     * @param array<mixed> $criteria
     *
     * @dataProvider getFilterWillApplyFilteringData
     * @throws FilterException
     */
    public function testFilterWillApplyFiltering(array $criteria): void
    {
        $fieldName = $criteria['field'] ?? 'testFieldName';
        $alias = $criteria['alias'] ?? null;

        $mapping = [
            'type' => 4
        ];

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        $this->metadata->expects($this->once())
            ->method('hasAssociation')
            ->with($fieldName)
            ->willReturn(true);

        $this->metadata->expects($this->once())
            ->method('getAssociationMapping')
            ->with($fieldName)
            ->willReturn($mapping);

        /** @var Expr&MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        /** @var Expr\Comparison&MockObject $comparisonExpr */
        $comparisonExpr = $this->createMock(Expr\Comparison::class);

        if (null === $alias) {
            $alias = 'entity';
            $this->queryBuilder->expects($this->once())
                ->method('getRootAlias')
                ->willReturn($alias);
        }

        $expressionString = $this->getExpressionString($fieldName, $alias, $criteria);

        $expr->expects($this->once())
            ->method($this->expressionMethodName)
            ->willReturn($comparisonExpr);

        $comparisonExpr->expects($this->once())
            ->method('__toString')
            ->willReturn($expressionString);

        $methodName = (!isset($criteria['where']) || WhereType::AND === $criteria['where'])
            ? 'andWhere'
            : 'orWhere';

        $this->queryBuilder->expects($this->once())->method($methodName);

        if (array_key_exists('value', $criteria)) {
            $this->typecaster->expects($this->once())
                ->method('typecast')
                ->with($this->metadata, $fieldName, $criteria['value'])
                ->willReturn($criteria['value']);

            $this->queryBuilder->expects($this->once())
                ->method('setParameter')
                ->with($this->callback(static function ($argument) {
                    return is_string($argument);
                }), $criteria['value']);
        }

        $this->filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * @param string      $fieldName
     * @param string|null $alias
     * @param array<mixed>      $criteria
     *
     * @return string
     */
    protected function getExpressionString(string $fieldName, ?string $alias, array $criteria): string
    {
        return ':param_name ' . $this->expressionSymbol . $alias . '.' . $fieldName;
    }

    /**
     * @return array<mixed>
     */
    public function getFilterWillApplyFilteringData(): array
    {
        return [
            [
                [
                    'name' => 'test',
                    'field' => 'hello',
                    'value' => 123,
                ],
            ],
        ];
    }
}
