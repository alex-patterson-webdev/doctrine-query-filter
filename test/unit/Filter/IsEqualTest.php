<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Constant\WhereType;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\IsEqual;
use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManager;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class IsEqualTest extends TestCase
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
     * @var TypecasterInterface|MockObject
     */
    private TypecasterInterface $typecaster;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManager::class);

        $this->metadata = $this->createMock(MetadataInterface::class);

        $this->queryBuilder = $this->createMock(QueryBuilderInterface::class);

        $this->typecaster = $this->createMock(TypecasterInterface::class);
    }

    /**
     * Assert that IsEqual implement FilterInterface
     */
    public function testImplementsFilterInterface(): void
    {
        $filter = new IsEqual($this->queryFilterManager, $this->typecaster);

        $this->assertInstanceOf(FilterInterface::class, $filter);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredFieldNameCriteriaIsMissing(): void
    {
        $filter = new IsEqual($this->queryFilterManager, $this->typecaster);

        $criteria = [
            // No field 'name' will raise exception
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The required \'field\' criteria value is missing for filter \'%s\'',
                IsEqual::class
            )
        );

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * Assert that the IsEqual query filter can be applied with the provided $criteria
     *
     * @throws InvalidArgumentException
     */
    public function testFilterWillApplyIsEqualFiltering(): void
    {
        $filter = new IsEqual($this->queryFilterManager, $this->typecaster);

        $fieldName = 'FieldNameTest';
        $criteria = [
            'field'  => $fieldName,
            'alias'  => 'test',
            'where'  => WhereType::AND,
            'value'  => 123,
            'format' => null,
        ];

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        /** @var Expr|MockObject $expr */
        $expr = $this->createMock(Expr::class);

        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        /** @var Expr\Comparison|MockObject $eq */
        $eq = $this->createMock(Expr\Comparison::class);

        $isEqualString = $criteria['alias'] . '.' . $fieldName . '=' . ':param_name';
        $expr->expects($this->once())
            ->method('eq')
            ->with($criteria['alias'] . '.' . $fieldName, $this->stringStartsWith(':'))
            ->willReturn($eq);

        $eq->expects($this->once())
            ->method('__toString')
            ->willReturn($isEqualString);

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

        $filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }
}
