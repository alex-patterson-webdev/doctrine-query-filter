<?php

namespace Arp\DoctrineQueryFilter\Entity\Repository;

use Arp\DoctrineQueryFilter\Service\QueryBuilder;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactory;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionManager;
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
     * @var QueryExpressionManager
     */
    protected $queryFilterManager;

    /**
     * $queryFilterFactory
     *
     * @var QueryExpressionFactoryInterface
     */
    protected $queryFilterFactory;

    /**
     * __construct.
     *
     * @param QueryExpressionFactoryInterface|null $queryFilterFactory
     * @param QueryExpressionManager|null          $queryFilterManager
     */
    public function __construct(
        QueryExpressionFactoryInterface $queryFilterFactory = null,
        QueryExpressionManager $queryFilterManager = null
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
     * @return QueryExpressionFactoryInterface
     */
    public function getQueryFilterFactory() : QueryExpressionFactoryInterface
    {
        if (! isset($this->queryFilterFactory)) {
            $this->queryFilterFactory = new QueryExpressionFactory($this->getQueryFilterManager());
        }

        return $this->queryFilterFactory;
    }

    /**
     * setQueryFilterFactory
     *
     * @param QueryExpressionFactoryInterface $queryFilterFactory
     */
    public function setQueryFilterFactory(QueryExpressionFactoryInterface $queryFilterFactory)
    {
        $this->queryFilterFactory = $queryFilterFactory;
    }

    /**
     * getQueryFilterManager
     *
     * Return the query filter manager.
     *
     * @return QueryExpressionManager
     */
    protected function getQueryFilterManager() : QueryExpressionManager
    {
        if (! isset($this->queryFilterManager)) {
            $this->queryFilterManager = new QueryExpressionManager();
        }
        return $this->queryFilterManager;
    }

    /**
     * setQueryFilterManager
     *
     * @param QueryExpressionManager $queryFilterManager
     */
    public function setQueryFilterManager(QueryExpressionManager $queryFilterManager)
    {
        $this->queryFilterManager = $queryFilterManager;
    }

}