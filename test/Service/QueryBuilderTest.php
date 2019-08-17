<?php

namespace ArpTest\Doctrine\QueryFilter;

use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Service\QueryBuilder;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\AbstractQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * QueryBuilderTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\Doctrine\DoctrineQueryFilter
 */
class QueryBuilderTest extends TestCase
{
    /**
     * $queryBuilder
     *
     * @var DoctrineQueryBuilder|MockObject
     */
    protected $queryBuilder;

    /**
     * $filterFactory
     *
     * @var QueryFilterFactoryInterface|MockObject
     */
    protected $filterFactory;

    /**
     * setUp
     *
     * Set up the test dependencies.
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->queryBuilder = $this->getMockBuilder(DoctrineQueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterFactory = $this->getMockForAbstractClass(QueryFilterFactoryInterface::class);
    }

    /**
     * testImplementsQueryBuilderInterface

     * @test
     */
    public function testImplementsQueryBuilderInterface()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $this->assertInstanceOf(QueryBuilderInterface::class, $service);
    }

    /**
     * testFactoryWillReturnTheFilterFactory
     *
     * Ensure that the filter factory is returned when calling factory().
     *
     * @test
     */
    public function testFactoryWillReturnTheFilterFactory()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $result = $service->factory();

        $this->assertInstanceOf(QueryFilterFactoryInterface::class, $result);
        $this->assertSame($this->filterFactory, $result);
    }

    /**
     * testSelect
     *
     * Ensure that the select $spec is proxied to the query builder select().
     *
     * @param null $spec  The select specification
     *
     * @test
     */
    public function testSelect($spec = null)
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $this->queryBuilder->expects($this->once())
            ->method('select')
            ->with($spec);

        $this->assertSame($service, $service->select($spec));
    }

    /**
     * testAddSelect
     *
     * Ensure calls to QueryBuilder->addSelect() will be proxied to the Doctrine query builder.
     *
     * @param mixed $spec
     *
     * @test
     */
    public function testAddSelect($spec = null)
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $this->queryBuilder->expects($this->once())
            ->method('addSelect')
            ->with($spec);

        $this->assertSame($service, $service->addSelect($spec));
    }

    /**
     * testFrom
     *
     * Ensure that the calls to from() will be proxied to the Doctrine Query Builder.
     *
     * @param string|null  $spec    The specification to test.
     * @param string|null  $alias   The optional test alias.
     * @param array        $options The optional test options.
     *
     * @dataProvider getFromData
     * @test
     */
    public function testFrom($spec = null, $alias = null, array $options = [])
    {
        $indexBy = isset($options['index_by']) ? $options['index_by'] : null;

        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $this->queryBuilder->expects($this->once())
            ->method('from')
            ->with($spec, $alias, $indexBy);

        $this->assertSame($service, $service->from($spec, $alias, $options));
    }

    /**
     * getFromData
     *
     * @return array
     */
    public function getFromData()
    {
        return  [

            [

            ],

        ];
    }

    /**
     * testWhereWithQueryFilter
     *
     * @test
     */
    public function testWhereWithQueryFilter()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        /** @var QueryFilterInterface|MockObject $queryFilter */
        $queryFilter = $this->getMockForAbstractClass(QueryFilterInterface::class);

        $dql = 'x != y AND z = 123';

        $queryFilter->expects($this->once())
            ->method('build')
            ->with($service)
            ->willReturn($dql);

        $this->queryBuilder->expects($this->once())
            ->method('where')
            ->with($dql);

        $this->assertSame($service, $service->where($queryFilter));
    }

    /**
     * testWhereWithString
     *
     * Ensure that where() will add a string criteria to the query builder and return self.
     *
     * @test
     */
    public function testWhereWithString()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $queryFilter = 'x != y AND z = 123';

        $this->queryBuilder->expects($this->once())
            ->method('where')
            ->with($queryFilter);

        $this->assertSame($service, $service->where($queryFilter));
    }

    /**
     * testAndWhereWithQueryFilter
     *
     * @test
     */
    public function testAndWhereWithQueryFilter()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        /** @var QueryFilterInterface|MockObject $queryFilter */
        $queryFilter = $this->getMockForAbstractClass(QueryFilterInterface::class);

        $dql = 'x != y AND z = 123';

        $queryFilter->expects($this->once())
            ->method('build')
            ->with($service)
            ->willReturn($dql);

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($dql);

        $this->assertSame($service, $service->andWhere($queryFilter));
    }

    /**
     * testAndWhereWithString
     *
     * Ensure that andWhere() will add a string criteria to the query builder and return self.
     *
     * @test
     */
    public function testAndWhereWithString()
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        $queryFilter = 'x != y AND z = 123';

        $this->queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with($queryFilter);

        $this->assertSame($service, $service->andWhere($queryFilter));
    }

    /**
     * testSetParameter
     *
     * @param string      $name
     * @param mixed       $value
     * @param string|null $type
     *
     * @dataProvider getSetParameterData
     * @test
     */
    public function testSetParameter($name, $value, $type = null)
    {
        /** @var QueryBuilder|MockObject $service */
        $service = $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$this->queryBuilder, $this->filterFactory])
            ->onlyMethods(['createParameterKey'])
            ->getMock();

        $key = $name . '_001';

        $service->expects($this->once())
            ->method('createParameterKey')
            ->with($name)
            ->willReturn($key);

        $this->queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with($key, $value, $type);

        $this->assertSame(':' . $key, $service->setParameter($name, $value, $type));
    }

    /**
     * getSetParameterData
     *
     * @return array
     */
    public function getSetParameterData()
    {
        return [

            [

                'name',
                'test',
                'string',
            ],

            [
                'foo',
                123,
                'integer',
            ]
        ];
    }

    /**
     * testGetQuery
     *
     * @param  array  $options
     *
     * @test
     */
    public function testGetQuery(array $options =  [])
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        /** @var AbstractQuery|MockObject $query */
        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass(); // Query is final :-/

        $this->queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->assertSame($query, $service->getQuery($options));
    }

    /**
     * testLimit
     *
     * @param integer      $limit
     * @param null|integer $offset
     *
     * @dataProvider getLimitData
     * @test
     */
    public function testLimit($limit, $offset = null)
    {
        /** @var QueryBuilder|MockObject $service */
        $service = $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$this->queryBuilder, $this->filterFactory])
            ->setMethods(['offset'])
            ->getMock();

        $this->queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with($limit);

        if ($offset) {
            $service->expects($this->once())
                ->method('offset')
                ->with($offset);
        }

        $this->assertSame($service, $service->limit($limit, $offset));
    }

    /**
     * getLimitData
     *
     * @return array
     */
    public function getLimitData()
    {
        return [
            [
                100
            ],

            [
                10,
                5
            ]
        ];
    }

    /**
     * testOffset
     *
     * @param integer      $offset
     * @param null|integer $limit
     *
     * @dataProvider getOffsetData
     * @test
     */
    public function testOffset($offset, $limit = null)
    {
        /** @var QueryBuilder|MockObject $service */
        $service = $this->getMockBuilder(QueryBuilder::class)
            ->setConstructorArgs([$this->queryBuilder, $this->filterFactory])
            ->setMethods(['limit'])
            ->getMock();

        $this->queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with($offset);

        if ($limit) {
            $service->expects($this->once())->method('limit')->with($limit);
        }

        $this->assertSame($service, $service->offset($offset, $limit));
    }

    /**
     * getOffsetData
     *
     * @return array
     */
    public function getOffsetData()
    {
        return [
            [
                100
            ],

            [
                0,
            ],

            [
                100,
                1000
            ],

            [
                0,
                10
            ]
        ];
    }

    /**
     * testConfigure
     *
     * Ensure that when calling configure we correctly set the limit/offset.
     *
     * @param array $options
     *
     * @dataProvider getConfigureData
     * @test
     */
    public function testConfigure(array $options = [])
    {
        $service = new QueryBuilder($this->queryBuilder, $this->filterFactory);

        if (array_key_exists('limit', $options)) {

            $this->queryBuilder->expects($this->once())
                ->method('setMaxResults')
                ->with($options['limit']);
        }

        if (array_key_exists('offset', $options)) {

            $this->queryBuilder->expects($this->once())
                ->method('setFirstResult')
                ->with($options['offset']);
        }

        $service->configure($options);
    }

    /**
     * getConfigureData
     *
     * @return array
     */
    public function getConfigureData()
    {
        return [

            [
                [
                    'limit' => 100
                ]
            ],

            [
                [
                    'offset' => 43
                ]
            ],

            [
                [
                    'limit'  => 12345,
                    'offset' => 98765,
                ],
            ],
        ];
    }

}