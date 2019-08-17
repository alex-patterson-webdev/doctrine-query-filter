<?php

namespace ArpTest\Doctrine\QueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
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
     * @var QueryFilterManager|MockObject
     */
    protected $filterManager;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() : void
    {
        $this->filterManager = $this->getMockBuilder(QueryFilterManager::class)
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
        $factory = new QueryFilterFactory($this->filterManager);

        $this->assertInstanceOf(QueryFilterFactoryInterface::class, $factory);
    }

}