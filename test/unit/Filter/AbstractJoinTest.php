<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Filter\AbstractJoin;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Composite;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractJoinTest extends AbstractFilterTest
{
    protected AbstractJoin $filter;

    protected string $filterClassName;

    public function setUp(): void
    {
        parent::setUp();

        /** @var AbstractJoin $filter */
        $filter = new $this->filterClassName($this->queryFilterManager, $this->typecaster, $this->paramNameGenerator);
        $this->filter = $filter;
    }

    public function testInstanceOfFilterInterface(): void
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function testInstanceOfAbstractJoin(): void
    {
        $this->assertInstanceOf(AbstractJoin::class, $this->filter);
    }

    /**
     * @throws FilterException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredFieldCriteriaIsNotProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The required \'field\' criteria value is missing for filter \'%s\'',
                $this->filterClassName
            )
        );

        $this->filter->filter($this->queryBuilder, $this->metadata, []);
    }

    /**
     * @throws FilterException
     */
    public function testFilterWillThrowInvalidArgumentExceptionIfProvidedAnInvalidFieldName(): void
    {
        $entityName = 'Test';
        $fieldName = 'foo';
        $criteria = [
            'field' => $fieldName,
        ];

        $this->metadata->expects($this->once())
            ->method('getName')
            ->willReturn($entityName);

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(false);

        $this->metadata->expects($this->once())
            ->method('hasAssociation')
            ->with($fieldName)
            ->willReturn(false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Unable to apply query filter \'%s\': '
                . 'The entity class \'%s\' has no field or association named \'%s\'',
                $this->filterClassName,
                $entityName,
                $fieldName
            )
        );

        $this->filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }

    /**
     * @param QueryBuilderInterface&MockObject $queryBuilder
     * @param string $fieldName
     * @param string $alias
     * @param null|string|Composite|Base $joinCondition
     * @param JoinConditionType|null $joinConditionType
     * @param string|null $indexBy
     */
    abstract protected function assertFilterJoin(
        QueryBuilderInterface $queryBuilder,
        string $fieldName,
        string $alias,
        null|string|Composite|Base $joinCondition = null,
        ?JoinConditionType $joinConditionType = null,
        ?string $indexBy = null
    ): void;

    /**
     * @throws InvalidArgumentException
     * @throws FilterException
     */
    public function testFilterWillApplyExpectedJoinWithoutConditions(): void
    {
        $fieldName = 'foo';
        $fieldAlias = 't';

        $joinAlias = 'a';
        $criteria = [
            'field' => $fieldAlias . '.' . $fieldName,
            'alias' => $joinAlias,
        ];

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        $this->assertFilterJoin($this->queryBuilder, $fieldAlias . '.' . $fieldName, $joinAlias);

        $this->filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }
}
