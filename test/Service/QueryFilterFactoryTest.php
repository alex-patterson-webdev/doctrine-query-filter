<?php

namespace ArpTest\Doctrine\QueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactory;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionManager;
use PHPUnit\Framework\TestCase;

/**
 * QueryFilterFactoryTest
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\Doctrine\DoctrineQueryFilter
 */
class QueryFilterFactoryTest extends TestCase
{
    /**
     * $filterManager
     *
     * @var QueryExpressionManager|MockObject
     */
    protected $filterManager;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->filterManager = $this->getMockBuilder(QueryExpressionManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * testImplementsQueryFilterFactoryInterface
     *
     * @test
     */
    public function testImplementsQueryFilterFactoryInterface()
    {
        $factory = new QueryExpressionFactory($this->filterManager);

        $this->assertInstanceOf(QueryExpressionFactoryInterface::class, $factory);
    }

}