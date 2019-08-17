<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\LessThan;
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * LessThanTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class LessThanTest extends AbstractQueryFilterTest
{

    /**
     * testImplementsQueryFilterInterface
     *
     * Ensure that the filter implements QueryFilterInterface
     *
     * @test
     */
    public function testImplementsQueryFilterInterface()
    {
        $filter = new LessThan(1, 2);

        $this->assertInstanceOf(QueryFilterInterface::class, $filter);
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
        $filter = new LessThan($a, $b);

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
                '1 < 2',
                1,
                2
            ]
        ];
    }

}