<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\NotEqual;
use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * NotEqualTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class NotEqualTest extends AbstractQueryFilterTest
{

    /**
     * testImplementsQueryFilterInterface
     *
     * Ensure that the filter implements QueryExpressionInterface
     *
     * @test
     */
    public function testImplementsQueryFilterInterface()
    {
        $filter = new NotEqual(1, 1);

        $this->assertInstanceOf(QueryExpressionInterface::class, $filter);
    }

    /**
     * testEqual
     *
     * @param string $expected
     * @param mixed  $a
     * @param mixed  $b
     *
     * @dataProvider getBuildData
     * @test
     */
    public function testBuild($expected, $a, $b)
    {
        $filter = new NotEqual($a, $b);

        $result = $filter->build($this->queryBuilder);

        $this->assertTrue(is_string($result));
        $this->assertEquals($expected, $result);
    }

    /**
     * getBuildData
     *
     * @return array
     */
    public function getBuildData()
    {
        return [
            [
                '1 <> 1',
                1,
                1
            ]
        ];
    }

}