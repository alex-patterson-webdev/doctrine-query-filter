<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\GreaterThan;
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * GreaterThanTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class GreaterThanTest extends AbstractQueryFilterTest
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
        $filter = new GreaterThan(1, 1);

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
        $filter = new GreaterThan($a, $b);

        $result = $filter->filter($this->queryBuilder,);

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
                '1 > 1',
                1,
                1
            ]
        ];
    }

}