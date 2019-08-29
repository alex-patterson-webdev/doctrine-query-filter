<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\IsNull;
use Arp\DoctrineQueryFilter\LessThan;
use Arp\DoctrineQueryFilter\QueryExpressionInterface;

/**
 * IsNullTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class IsNullTest extends AbstractQueryFilterTest
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
        $filter = new IsNull('foo', 'f');

        $this->assertInstanceOf(QueryExpressionInterface::class, $filter);
    }

    /**
     * testEqual
     *
     * @param string $expected
     * @param mixed  $fieldName
     * @param mixed  $alias
     *
     * @dataProvider getBuildData
     * @test
     */
    public function testBuild($expected, $fieldName, $alias = null)
    {
        $filter = new IsNull($fieldName, $alias);

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
                'test IS NULL',
                'test'
            ],

            [
                'foo.test IS NULL',
                'foo.test'
            ],

            [
                'a.hello123 IS NULL',
                'hello123',
                'a',
            ],

            [
                'b.foo_bar_hello IS NULL',
                'foo_bar_hello',
                'b',
            ],

            // We defined a alias twice
            [
                'b.test IS NULL',
                'b.test',
                'a',
            ],
        ];
    }

}