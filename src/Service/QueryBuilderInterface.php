<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;
use Doctrine\ORM\AbstractQuery;

/**
 * QueryBuilderInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryBuilderInterface
{
    /**
     * configure
     *
     * Configure the query builder instance.
     *
     * @param array $options  The configuration options to set.
     *
     * @return $this
     */
    public function configure(array $options = []) : QueryBuilderInterface;

    /**
     * filter
     *
     * Return the query filter factory.
     *
     * @return QueryFilterFactoryInterface
     */
    public function factory() : QueryFilterFactoryInterface;

    /**
     * select
     *
     * Create a select expression.
     *
     * @param array  $spec
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function select($spec) : QueryBuilderInterface;

    /**
     * addSelect
     *
     * @param string|array $spec
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function addSelect($spec) : QueryBuilderInterface;

    /**
     * from
     *
     * @param mixed       $spec
     * @param string|null $alias
     * @param array       $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function from($spec, string $alias = null, array $options = []) : QueryBuilderInterface;

    /**
     * join
     *
     * @param string                       $spec
     * @param string                       $alias
     * @param QueryFilterInterface|string  $conditions
     * @param array                        $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function join(string $spec, string $alias, $conditions, array $options = []) : self;

    /**
     * where
     *
     * Set the where query expression.
     *
     * @param QueryFilterInterface|string $queryFilter
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function where($queryFilter) : QueryBuilderInterface;

    /**
     * andWhere
     *
     * Append a new where query expression to the collection.
     *
     * @param QueryFilterInterface|string $queryFilter
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function andWhere($queryFilter) : QueryBuilderInterface;

    /**
     * limit
     *
     * Add a limit query expression.
     *
     * @param int      $limit
     * @param null|int $offset
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function limit(int $limit, int $offset = null) : QueryBuilderInterface;

    /**
     * offset
     *
     * Add an offset query express.
     *
     * @param integer      $offset
     * @param null|integer $limit
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function offset(int $offset, int $limit = null) : QueryBuilderInterface;

    /**
     * getAlias
     *
     * Return the parent (root) alias.
     *
     * @return string
     */
    public function getAlias() : string;

    /**
     * getAliases
     *
     * Return a collection of all the query aliases currently within the builder.
     *
     * @return array
     */
    public function getAliases() : array;

    /**
     * setParameter
     *
     * Set a single parameter value.
     *
     * @param string       $name   The name of the parameter.
     * @param mixed        $value  The value of the parameter.
     * @param string|null  $type   Optional parameter type string.
     *
     * @return string
     *
     * @throws QueryBuilderException
     */
    public function setParameter($name, $value, $type = null) : string;

    /**
     * setParameters
     *
     * Remove existing parameters and replace them with a new collection.
     *
     * @param array $params  The new parameters collection to set.
     *
     * @return array
     *
     * @throws QueryBuilderException
     */
    public function setParameters(array $params) : array;

    /**
     * getQuery
     *
     * Create and return a new query using the configured criteria.
     *
     * @param array $options  Optional creation options.
     *
     * @return AbstractQuery
     *
     * @throws QueryBuilderException
     */
    public function getQuery(array $options = []) : AbstractQuery;


}