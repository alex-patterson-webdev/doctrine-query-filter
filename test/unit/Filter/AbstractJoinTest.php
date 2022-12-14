<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Enum\JoinConditionType;
use Arp\DoctrineQueryFilter\Filter\AbstractJoin;
use Arp\DoctrineQueryFilter\Filter\AndX;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
use Arp\DoctrineQueryFilter\Filter\Exception\InvalidArgumentException;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx as DoctrineAndX;
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
    public function testFilterWillThrowInvalidArgumentExceptionIfTheRequiredJoinAliasIsNotProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The required \'alias\' criteria value is missing for filter \'%s\'',
                $this->filterClassName
            )
        );

        $fieldName = 'foo';
        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        $criteria = [
            'field' => 'x.' . $fieldName,
        ];

        $this->filter->filter($this->queryBuilder, $this->metadata, $criteria);
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

    /**
     * @throws InvalidArgumentException
     * @throws FilterException
     */
    public function testFilterWillApplyExpectedJoinWithConditions(): void
    {
        $fieldName = 'foo';
        $fieldAlias = 't';
        $joinAlias = 'a';

        $conditions = [
            [
                'name' => 'eq',
                'field' => 'id',
                'alias' => $fieldAlias,
                'value' => $joinAlias . '.id',
            ]
        ];

        $criteria = [
            'field' => $fieldAlias . '.' . $fieldName,
            'alias' => $joinAlias,
            'condition_type' => JoinConditionType::WITH->value,
            'conditions' => $conditions,
        ];

        $associationMapping = [
            'targetEntity' => 'Bar',
        ];

        $this->metadata->expects($this->once())
            ->method('getAssociationMapping')
            ->with($fieldName)
            ->willReturn($associationMapping);

        $this->metadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        /** @var QueryBuilderInterface&MockObject $conditionsQueryBuilder */
        $conditionsQueryBuilder = $this->createMock(QueryBuilderInterface::class);
        $this->queryBuilder->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($conditionsQueryBuilder);

        $this->queryFilterManager->expects($this->once())
            ->method('filter')
            ->with(
                $conditionsQueryBuilder,
                $associationMapping['targetEntity'],
                [
                    'filters' => [
                        [
                            'name' => AndX::class,
                            'conditions' => $conditions,
                            'where' => null,
                        ]
                    ]
                ]
            );

        /** @var DoctrineAndX&MockObject $andX */
        $andX = $this->createMock(DoctrineAndX::class);
        $queryParts = [
            'where' => $andX,
        ];
        $andXParts = [
            $fieldAlias . '.id = ' . $joinAlias . '.id',
        ];

        $conditionsQueryBuilder->expects($this->once())
            ->method('getQueryParts')
            ->willReturn($queryParts);

        /** @var Expr&MockObject $expr */
        $expr = $this->createMock(Expr::class);
        $this->queryBuilder->expects($this->once())
            ->method('expr')
            ->willReturn($expr);

        /** @var DoctrineAndX&MockObject $conditionAndX */
        $conditionAndX = $this->createMock(DoctrineAndX::class);
        $expr->expects($this->once())
            ->method('andX')
            ->willReturn($conditionAndX);

        $conditionAndX->expects($this->once())
            ->method('addMultiple')
            ->willReturn($andXParts);

        $this->queryBuilder->expects($this->once())
            ->method('mergeParameters')
            ->with($conditionsQueryBuilder);

        $this->assertFilterJoin(
            $this->queryBuilder,
            $fieldAlias . '.' . $fieldName,
            $joinAlias,
            $conditionAndX,
            JoinConditionType::WITH
        );

        $this->filter->filter($this->queryBuilder, $this->metadata, $criteria);
    }
}
