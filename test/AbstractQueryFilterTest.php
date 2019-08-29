<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Query\Expr;

/**
 * AbstractQueryFilterTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
abstract class AbstractQueryFilterTest extends TestCase
{

    /**
     * $queryBuilder
     *
     * @var QueryBuilderInterface|MockObject
     */
    protected $queryBuilder;

    /**
     * $expr
     *
     * @var Expr|MockObject
     */
    protected $expr;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->queryBuilder = $this->getMockForAbstractClass(QueryBuilderInterface::class);

        $this->expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * testImplementsQueryFilterInterface
     *
     * Ensure that the filter implements QueryExpressionInterface
     *
     * @test
     */
    abstract public function testImplementsQueryFilterInterface();
}