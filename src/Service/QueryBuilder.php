<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\Join;
use Arp\DoctrineQueryFilter\From;
use Arp\DoctrineQueryFilter\Having;
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Where;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;
use Doctrine\ORM\Query\Expr;
use Exception;

/**
 * QueryBuilder
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class QueryBuilder implements QueryBuilderInterface
{
    /**
     * $queryBuilder
     *
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * $queryFilterFactory
     *
     * @var QueryFilterFactoryInterface
     */
    protected $queryFilterFactory;

    /**
     * __construct.
     *
     * @param \Doctrine\ORM\QueryBuilder  $queryBuilder
     * @param QueryFilterFactoryInterface $queryFilterFactory
     */
    public function __construct(\Doctrine\ORM\QueryBuilder $queryBuilder, QueryFilterFactoryInterface $queryFilterFactory)
    {
        $this->queryBuilder = $queryBuilder;
        $this->queryFilterFactory = $queryFilterFactory;
    }

    /**
     * getDoctrineQueryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getDoctrineQueryBuilder() : \Doctrine\ORM\QueryBuilder
    {
        return $this->queryBuilder;
    }

    /**
     * $expr
     *
     * Return the expression builder for Doctrine ORM.
     *
     * @return Expr
     */
    public function expr() : Expr
    {
        return $this->queryBuilder->expr();
    }

    /**
     * getFilterFactory
     *
     * Return the query expression factory.
     *
     * @return QueryFilterFactoryInterface
     */
    public function getFilterFactory() : QueryFilterFactoryInterface
    {
        return $this->queryFilterFactory;
    }

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
    public function select($spec) : QueryBuilderInterface
    {
        try {
            $this->queryBuilder->select($spec);
        }
        catch (\Exception $e) {

            throw new QueryBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

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
    public function addSelect($spec) : QueryBuilderInterface
    {
        try {
            $this->queryBuilder->addSelect($spec);
        }
        catch (\Exception $e) {

            throw new QueryBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

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
    public function from($spec, string $alias, array $options = []) : QueryBuilderInterface
    {
        try {
            $this->queryBuilder->from($spec, $alias, $options);
        }
        catch (\Exception $e) {

            throw new QueryBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

    /**
     * innerJoin
     *
     * @param string $join
     * @param string $alias
     * @param null   $conditions
     * @param array  $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function innerJoin(string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface
    {
        return $this->join(Join::JOIN_INNER, $join, $alias, $conditions, $options);
    }

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
    public function leftJoin(string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface
    {
        return $this->join(Join::JOIN_LEFT, $join, $alias, $conditions, $options);
    }

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
    public function join(string $type, string $join, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface
    {
        $indexBy       = isset($options['index_by'])       ? $options['index_by']       : null;
        $conditionType = isset($options['condition_type']) ? $options['condition_type'] : null;

        if (isset($conditions)
            && ! is_string($conditions)
            && (! $conditions instanceof QueryFilterInterface)
        ) {
            $conditions = $this->factory()->create($conditions);
        }

        if ($conditions instanceof QueryFilterInterface) {
            $conditions->filter($this,);
        }

        try {
            $this->queryBuilder->join($join, $alias, $conditionType, $conditions, $indexBy);
        }

        catch (\Exception $e) {

            throw new QueryBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

    /**
     * where
     *
     * Set the where query expression.
     *
     * @param mixed $spec
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function where($spec) : QueryBuilderInterface
    {
        try {
            if (isset($spec) && ! is_string($spec) && ! $spec instanceof QueryFilterInterface) {
                $spec = $this->factory()->create($spec);
            }

            if ($spec instanceof QueryFilterInterface) {
                $spec->filter($this,);
            }
        }
        catch (Exception $e) {

            throw new QueryBuilderException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * andWhere
     *
     * Append a new where query expression to the collection.
     *
     * @param mixed $spec
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function andWhere($spec) : QueryBuilderInterface
    {
        try {
            $this->dqlParts['where'][] = $this->expr()->create(Where::class, func_get_args());
        }
        catch(Exception $e) {

            throw new QueryBuilderException(
                sprintf(
                    'Failed to add specification : %s',
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

    /**
     * having
     *
     * @param mixed $expression
     *
     * @return QueryBuilderInterface
     *
     * @throws QueryBuilderException
     */
    public function having($expression) : QueryBuilderInterface
    {
        try {
            $this->dqlParts['having'][] = $this->expr()->create(Having::class, func_get_args());
        }
        catch(Exception $e) {

            throw new QueryBuilderException(
                sprintf(
                    'Failed to add specification : %s',
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

    /**
     * orderBy
     *
     * @param string $field
     * @param string $direction
     *
     * @return QueryBuilderInterface
     */
    public function orderBy(string $field, string $direction = null) : QueryBuilderInterface
    {

    }

    /**
     * setFirstResult
     *
     * @param int|null $firstResult
     *
     * @return $this
     */
    public function setFirstResult(int $firstResult = null) : QueryBuilderInterface
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * setMaxResults
     *
     * @param int|null $maxResults
     *
     * @return $this
     */
    public function setMaxResults(int $maxResults = null) : QueryBuilderInterface
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * getAlias
     *
     * Return the parent (root) alias.
     *
     * @return string
     */
    public function getAlias() : string
    {
        $aliases = $this->getAliases();

        return empty($aliases[0]) ? '' : $aliases[0];
    }

    /**
     * getAliases
     *
     * Return a collection of all the query aliases currently within the builder.
     *
     * @return array
     */
    public function getAliases() : array
    {
        return [];
    }

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
    public function setParameter(string $name, $value, $type = null) : QueryBuilderInterface
    {
       $this->parameters[$name] = compact('name', 'value', 'type');

       return $this;
    }

    /**
     * setParameters
     *
     * Remove existing parameters and replace them with a new collection.
     *
     * @param array $params  The new parameters collection to set.
     *
     * @return $this
     */
    public function setParameters(array $params) : QueryBuilderInterface
    {
        $this->parameters = [];

        foreach($params as $name => $value) {
            $this->setParameter($name, $value);
        }

        return $this;
    }

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
    public function getQuery(array $options = []) : QueryInterface
    {
        try {
            $query = $this->entityManager->createQuery($this->getDQL());

            foreach ($this->parameters as $parameter) {

                if (isset($parameter['name'], $parameter['value'])) {
                    $query->setParameter(
                        $parameter['name'],
                        $parameter['value'],
                        (isset($parameter['type']) ? $parameter['type'] : null)
                    );
                }
            }

            if (isset($this->firstResult)) {
                $query->setFirstResult($this->firstResult);
            }

            if (isset($this->maxResults)) {
                $query->setMaxResults($this->maxResults);
            }

            return new Query($query, $options);
        }
        catch(Exception $e) {

            throw new QueryBuilderException(
                sprintf(
                    'Failed to add specification : %s',
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * configure
     *
     * Configure the query builder instance.
     *
     * @param array $options  The configuration options to set.
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function configure(array $options = []) : QueryBuilderInterface
    {
        try {
            foreach ($options as $name => $value) {
                switch ($name) {
                    case 'first_result' :
                        $this->setFirstResult($value);
                    break;

                    case 'max_results' :
                        $this->setMaxResults($value);
                    break;
                }
            }
        }
        catch(\Exception $e) {

            throw new QueryBuilderException(
                sprintf(
                    'Unable to configure query builder : %s',
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        return $this;
    }

}