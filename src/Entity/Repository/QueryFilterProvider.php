<?php

namespace Arp\DoctrineQueryFilter\Entity\Repository;

use Arp\DoctrineQueryFilter\Service\QueryBuilder;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactoryInterface;
use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Doctrine\ORM\EntityManager;

/**
 * QueryFilterProvider
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Entity\Repository
 */
class QueryFilterProvider
{
    /**
     * $queryFilterManager
     *
     * @var QueryFilterManager
     */
    protected $queryFilterManager;

    /**
     * $queryFilterFactory
     *
     * @var QueryFilterFactoryInterface
     */
    protected $queryFilterFactory;

    /**
     * __construct.
     *
     * @param QueryFilterFactoryInterface|null $queryFilterFactory
     * @param QueryFilterManager|null          $queryFilterManager
     */
    public function __construct(
        QueryFilterFactoryInterface $queryFilterFactory = null,
        QueryFilterManager $queryFilterManager = null
    ){
        $this->queryFilterFactory = $queryFilterFactory;
        $this->queryFilterManager = $queryFilterManager;
    }

    /**
     * createQueryBuilder
     *
     * @param EntityManager $entityManager
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder(EntityManager $entityManager) : QueryBuilderInterface
    {
        return new QueryBuilder(
            $entityManager->createQueryBuilder(),
            $this->getQueryFilterFactory()
        );
    }

    /**
     * getQueryFilterFactory
     *
     * @return QueryFilterFactoryInterface
     */
    public function getQueryFilterFactory() : QueryFilterFactoryInterface
    {
        if (! isset($this->queryFilterFactory)) {
            $this->queryFilterFactory = new QueryFilterFactory($this->getQueryFilterManager());
        }

        return $this->queryFilterFactory;
    }

    /**
     * setQueryFilterFactory
     *
     * @param QueryFilterFactoryInterface $queryFilterFactory
     */
    public function setQueryFilterFactory(QueryFilterFactoryInterface $queryFilterFactory)
    {
        $this->queryFilterFactory = $queryFilterFactory;
    }

    /**
     * getQueryFilterManager
     *
     * Return the query filter manager.
     *
     * @return QueryFilterManager
     */
    protected function getQueryFilterManager() : QueryFilterManager
    {
        if (! isset($this->queryFilterManager)) {
            $this->queryFilterManager = new QueryFilterManager();
        }
        return $this->queryFilterManager;
    }

    /**
     * setQueryFilterManager
     *
     * @param QueryFilterManager $queryFilterManager
     */
    public function setQueryFilterManager(QueryFilterManager $queryFilterManager)
    {
        $this->queryFilterManager = $queryFilterManager;
    }

}