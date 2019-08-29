<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\IsNotNull;
use Arp\DoctrineQueryFilter\QueryExpressionInterface;

/**
 * IsNotNullTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class IsNotNullTest extends AbstractQueryFilterTest
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
        $filter = new IsNotNull('foo', 'f');

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
        $filter = new IsNotNull($fieldName, $alias);

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
                'test IS NOT NULL',
                'test'
            ],

            [
                'foo.test IS NOT NULL',
                'foo.test'
            ],

            [
                'a.hello123 IS NOT NULL',
                'hello123',
                'a',
            ],

            [
                'b.test IS NOT NULL',
                'test',
                'b',
            ],

            // We defined a alias twice
            [
                'b.test IS NOT NULL',
                'b.test',
                'a',
            ],
        ];
    }

}