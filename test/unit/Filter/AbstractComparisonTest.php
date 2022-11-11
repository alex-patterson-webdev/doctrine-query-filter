<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractComparisonTest extends AbstractFilterTest
{
    protected FilterInterface $filter;

    protected string $filterClassName;

    protected string $expressionMethodName;

    protected string $expressionSymbol;

    public function setUp(): void
    {
        parent::setUp();

        /** @var FilterInterface $filter */
        $filter = new $this->filterClassName($this->queryFilterManager, $this->typecaster);
        $this->filter = $filter;
    }

    /**
     * Assert that the filter implements FilterInterface
     */
    public function testImplementsFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    /**
     * Assert that the query filter can be applied with the provided $criteria
     *
     * @param array<mixed> $criteria
     *
     * @dataProvider getFilterWillApplyFilteringData
     *
     * @throws FilterException
     */
    public function testFilterWillApplyFiltering(array $criteria): void
    {
        $fieldName = $criteria['field'] ?? 'testFieldName';
        $alias = $criteria['alias'] ?? null;

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

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
     * @return array<mixed>
     */
    abstract public function getFilterWillApplyFilteringData(): array;

    /**
     * @param string      $fieldName
     * @param string|null $alias
     * @param array<mixed>      $criteria
     *
     * @return string
     */
    protected function getExpressionString(string $fieldName, ?string $alias, array $criteria): string
    {
        $expressionString = $alias . '.' . $fieldName . ' ' . $this->expressionSymbol;
        if (array_key_exists('value', $criteria)) {
            $expressionString .= ' :param_name';
        }
        return $expressionString;
    }
}
