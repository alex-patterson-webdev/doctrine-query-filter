<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;

/**
 * QueryBuilderInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryBuilderInterface
{
    /**
     * getDQL
     *
     * Return the DQL string representation.
     *
     * @return string
     */
    public function getDQL() : string;

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
     * expr
     *
     * Return the query expression factory.
     *
     * @return QueryExpressionFactoryInterface
     */
    public function expr() : QueryExpressionFactoryInterface;

    /**
     * select
     *
     * Create a select expression.
     *
     * @param array|string  $spec
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
     * @param mixed  $spec
     * @param string $alias
     * @param array  $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function from($spec, string $alias, array $options = []) : QueryBuilderInterface;

    /**
     * join
     *
     * @param string  $type
     * @param string  $join
     * @param string  $alias
     * @param mixed   $conditions
     * @param array   $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function join(string $type, string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface;

    /**
     * leftJoin
     *
     * @param string  $join
     * @param string  $alias
     * @param mixed   $conditions
     * @param array   $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function leftJoin(string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface;

    /**
     * innerJoin
     *
     * @param string  $join
     * @param string  $alias
     * @param mixed   $conditions
     * @param array   $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function innerJoin(string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface;

    /**
     * where
     *
     * Set the where query expression.
     *
     * @param QueryExpressionInterface|string $expression
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function where($expression) : QueryBuilderInterface;

    /**
     * andWhere
     *
     * Append a new where query expression to the collection.
     *
     * @param QueryExpressionInterface|string $expression
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function andWhere($expression) : QueryBuilderInterface;

    /**
     * having
     *
     * @param $expression
     *
     * @return QueryBuilderInterface
     */
    public function having($expression) : QueryBuilderInterface;

    /**
     * orderBy
     *
     * @param string $field
     * @param string $direction
     *
     * @return QueryBuilderInterface
     */
    public function orderBy(string $field, string $direction = null) : QueryBuilderInterface;

    /**
     * setFirstResult
     *
     * @param int|null $firstResult
     *
     * @return $this
     */
    public function setFirstResult(int $firstResult = null) : QueryBuilderInterface;

    /**
     * setMaxResults
     *
     * @param int|null $maxResults
     *
     * @return $this
     */
    public function setMaxResults(int $maxResults = null) : QueryBuilderInterface;

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
     * @return $this
     */
    public function setParameter(string $name, $value, $type = null) : QueryBuilderInterface;

    /**
     * setParameters
     *
     * Remove existing parameters and replace them with a new collection.
     *
     * @param array $params  The new parameters collection to set.
     *
     * @return $this
     */
    public function setParameters(array $params) : QueryBuilderInterface;

    /**
     * getQuery
     *
     * Create and return a new query using the configured criteria.
     *
     * @param array $options  Optional creation options.
     *
     * @return QueryInterface
     *
     * @throws QueryBuilderException
     */
    public function getQuery(array $options = []) : QueryInterface;

}