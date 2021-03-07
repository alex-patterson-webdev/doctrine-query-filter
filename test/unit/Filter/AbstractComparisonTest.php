<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
abstract class AbstractComparisonTest extends AbstractFilterTest
{
    /**
     * @var FilterInterface
     */
    protected FilterInterface $filter;

    /**
     * @var string
     */
    protected string $filterClassName;

    /**
     * @var string
     */
    protected string $expressionMethodName;

    /**
     * @var string
     */
    protected string $expressionSymbol;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->filter = new $this->filterClassName($this->queryFilterManager, $this->typecaster);
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
     * @param array $criteria
     *
     * @dataProvider getFilterWillApplyFilteringData
     */
    public function testFilterWillApplyFiltering(array $criteria): void
    {
        $fieldName = $criteria['field'] ?? 'testFieldName';
        $alias = $criteria['alias'] ?? null;

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        /** @var Expr|MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        /** @var Expr\Comparison|MockObject $comparisonExpr */
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
     * @return array
     */
    abstract public function getFilterWillApplyFilteringData(): array;

    /**
     * @param string      $fieldName
     * @param string|null $alias
     * @param array       $criteria
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
