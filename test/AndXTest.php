<?php

namespace ArpTest\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\AndX;
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * AndXTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter
 */
class AndXTest extends AbstractQueryFilterTest
{
    /**
     * testImplementsQueryFilterInterface
     *
     * Ensure that the class implements QueryFilterInterface.
     *
     * @test
     */
    public function testImplementsQueryFilterInterface()
    {
        $filter = new AndX();

        $this->assertInstanceOf(QueryFilterInterface::class, $filter);
    }

    /**
     * testBuild
     *
     * Ensure that the AndX query filter will combine all registered query filters and perform the build()
     * returning the correct expression string.
     *
     * @test
     */
    public function testBuild()
    {
        /** @var QueryFilterInterface[]|MockObject[] $filters */
        $filters = [
            $this->getMockForAbstractClass(QueryFilterInterface::class),
            $this->getMockForAbstractClass(QueryFilterInterface::class),
            $this->getMockForAbstractClass(QueryFilterInterface::class),
            $this->getMockForAbstractClass(QueryFilterInterface::class),
            $this->getMockForAbstractClass(QueryFilterInterface::class),
        ];

        $andXFilter = new AndX(...$filters);

        $expressions = [
            '1 = 1',
            '2 = 2',
            '3 = 3',
            '4 = 4',
            '5 = 5',
        ];

        $addArgs = [];
        foreach($filters as $index => $filter) {
            $addArgs[] = [$expressions[$index]];

            $filter->expects($this->once())
                ->method('build')
                ->with($this->queryBuilder)
                ->willReturn($expressions[$index]);
        }

        $expressionsString = implode(' AND ', $expressions);

        $result = $andXFilter->filter($this->queryBuilder,);

        $this->assertTrue(is_string($result));
        $this->assertEquals($expressionsString, $result);
    }

    /**
     * testBuildWillReturnEmptyStringWhenQueryFiltersAreEmpty
     *
     * Ensure that we return just an empty string if there are no query filters attached.
     *
     * @test
     */
    public function testBuildWillReturnEmptyStringWhenQueryFiltersAreEmpty()
    {
        $queryFilter = new AndX;

        $this->assertSame('', $queryFilter->filter($this->queryBuilder,));
    }

}