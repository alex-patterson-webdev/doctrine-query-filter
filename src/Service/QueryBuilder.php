<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
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
     * @var DoctrineQueryBuilder
     */
    protected $queryBuilder;

    /**
     * $filterFactory
     *
     * @var QueryExpressionFactoryInterface
     */
    protected $expressionFactory;

    /**
     * __construct
     *
     * @param DoctrineQueryBuilder            $queryBuilder
     * @param QueryExpressionFactoryInterface $expressionFactory
     */
    public function __construct(DoctrineQueryBuilder $queryBuilder, QueryExpressionFactoryInterface $expressionFactory)
    {
        $this->queryBuilder  = $queryBuilder;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * getDQL
     *
     * Return the DQL string representation.
     *
     * @return string
     */
    public function getDQL() : string
    {
        return $this->queryBuilder->getDQL();
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
        if (! empty($options)) {

            try {
                foreach ($options as $name => $value) {
                    switch ($name) {

                        case 'limit' :
                            $this->limit($value);
                        break;

                        case 'offset' :
                            $this->offset($value);
                        break;

                        case 'order_by' :
                            if (is_array($value)) {
                                foreach ($value as $fieldName => $direction) {
                                    $this->queryBuilder->orderBy($fieldName, $direction);
                                }
                            }
                        break;
                    }
                }
            }
            catch (QueryBuilderException $e) {
                throw $e;
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
        }

        return $this;
    }

    /**
     * expr
     *
     * Return the query expression factory.
     *
     * @return QueryExpressionFactoryInterface
     */
    public function expr() : QueryExpressionFactoryInterface
    {
        return $this->expressionFactory;
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
        catch(\Exception $e) {

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
        $indexBy = isset($options['index_by']) ? $options['index_by'] : null;

        try {
            $this->queryBuilder->from($spec, $alias, $indexBy);
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
     * join
     *
     * @param string  $spec
     * @param string  $alias
     * @param mixed   $conditions
     * @param array   $options
     *
     * @return $this
     *
     * @throws QueryBuilderException
     */
    public function join(string $spec, string $alias, $conditions = null, array $options = []) : QueryBuilderInterface
    {
        $indexBy = isset($options['index_by']) ? $options['index_by'] : null;
        $type    = isset($options['type'])     ? $options['type']     : Expr\Join::WITH;

        try {
            if (isset($conditions) && ! is_string($conditions)) {
                $conditions = $this->expressionFactory->create($conditions);
            }

            if ($conditions instanceof QueryExpressionInterface) {
                $conditions = $conditions->build($this->expressionFactory);
            }

            if (! is_string($conditions) || empty($conditions)) {
                $conditions = null;
            }

            $this->queryBuilder->join(
                $spec,
                $alias,
                $type,
                $conditions,
                $indexBy
            );
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
            if (! is_string($spec)) {
                $spec = $this->expressionFactory->create($spec);
            }

            if ($spec instanceof QueryExpressionInterface) {
                $spec = $spec->build($this->expressionFactory);
            }

            if (! empty($spec)) {
                $this->queryBuilder->where($spec);
            }
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
            if (! is_string($spec)) {
                $spec = $this->expressionFactory->create($spec);
            }

            if ($spec instanceof QueryExpressionInterface) {
                $spec = $spec->build($this->expressionFactory);
            }

            if (! empty($spec)) {
                $this->queryBuilder->where($spec);
            }
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
    public function limit(int $limit, int $offset = null) : QueryBuilderInterface
    {
        try {
            $this->queryBuilder->setMaxResults($limit);

            if ($offset) {
                $this->offset($offset);
            }

        }
        catch (QueryBuilderException $e) {
            throw $e;
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
    public function offset(int $offset, int $limit = null) : QueryBuilderInterface
    {
        try {
            $this->queryBuilder->setFirstResult($offset);

            if ($limit) {
                $this->limit($limit);
            }

        }
        catch (QueryBuilderException $e) {
            throw $e;
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
        return $this->queryBuilder->getRootAliases();
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
     * @return string
     *
     * @throws QueryBuilderException
     */
    public function setParameter($name, $value, $type = null) : string
    {
        $key = $this->createParameterKey($name);

        try {
            $this->queryBuilder->setParameter($key, $value, $type);
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

        return ':' . $key;
    }

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
    public function setParameters(array $params) : array
    {
        $keys = [];

        foreach($params as $name => $param) {
            $keys[$name] = $this->setParameter($name, $param);
        }

        return $keys;
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
        if (! empty($options)) {
            $this->configure($options);
        }

        try {
            return new Query($this->queryBuilder->getQuery());
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
     * createParameterKey
     *
     * Create a new key to use as a placeholder for a parameter value.
     *
     * @param string $name  The key name or index.
     *
     * @return string|int
     */
    protected function createParameterKey($name)
    {
        if (is_int($name)) {
            return $name;
        }

        return uniqid($name);
    }

}