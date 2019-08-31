<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\From;
use Arp\DoctrineQueryFilter\Having;
use Arp\DoctrineQueryFilter\Join;
use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Arp\DoctrineQueryFilter\Select;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;
use Arp\DoctrineQueryFilter\Where;
use Doctrine\ORM\EntityManager;
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
     * $entityManager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * $dqlParts
     *
     * @var QueryExpressionInterface[]
     */
    protected $dqlParts = [
        'distinct' => false,
        'delete'   => [],
        'update'   => [],
        'select'   => [],
        'from'     => [],
        'join'     => [],
        'where'    => [],
        'having'   => [],
        'order_by' => [],
    ];

    /**
     * $queryType
     *
     * @var string
     */
    protected $queryType;

    /**
     * $parameters
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * $filterFactory
     *
     * @var QueryExpressionFactoryInterface
     */
    protected $expressionFactory;

    /**
     * $maxResults
     *
     * @var integer|null
     */
    protected $maxResults;

    /**
     * $firstResult
     *
     * @var integer|null
     */
    protected $firstResult;

    /**
     * __construct
     *
     * @param EntityManager                   $entityManager
     * @param QueryExpressionFactoryInterface $expressionFactory
     */
    public function __construct(EntityManager $entityManager, QueryExpressionFactoryInterface $expressionFactory)
    {
        $this->entityManager     = $entityManager;
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
        $dql = '';

        switch ($this->queryType) {
            case 'SELECT' :
                $dql = $this->createSelectDQL();
            break;
        }

        return $dql;
    }

    /**
     * createSelectDQL
     *
     * @return string
     */
    protected function createSelectDQL()
    {
        $dql = 'SELECT';

        if (isset($this->dqlParts['distinct']) && true === $this->dqlParts['distinct']) {
            $dql .= 'DISTINCT';
        }

        /** @var Select $expression */
        $selectParts = [];
        foreach ($this->dqlParts['select'] as $expression) {
            $selectParts[] = $expression->build($this);
        }
        $dql .= implode(', ', $selectParts);
        $dql .= ' FROM ';

        /** @var From $expression */
        foreach($this->dqlParts['from'] as $expression) {
            $dql .= $expression->build($this);
        }

        if (! empty($this->dqlParts['join'])) {
            /** @var Join $expression */
            foreach($this->dqlParts['join'] as $expression) {
                $dql .= $expression->build($this);
            }
        }

        if (! empty($this->dqlParts['where'])) {
            $dql .= ' WHERE ';

            /** @var QueryExpressionInterface $expression */
            foreach($this->dqlParts['where'] as $expression) {
                $dql .= $expression->build($this);
            }
        }

        if (! empty($this->dqlParts['having'])) {
            $dql .= ' HAVING ';

            /** @var QueryExpressionInterface $expression */
            foreach($this->dqlParts['having'] as $expression) {
                $dql .= $expression->build($this);
            }
        }

        if (! empty($this->dqlParts['order_by'])) {
            $dql .= ' ORDER BY ';

            /** @var QueryExpressionInterface $expression */
            foreach($this->dqlParts['order_by'] as $expression) {
                $dql .= $expression->build($this);
            }
        }

        return $dql;
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
        $this->queryType = 'SELECT';

        $this->dqlParts['select'] = [];

        return $this->addSelect($spec);
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
        if (empty($this->queryType)) {
            $this->queryType = 'SELECT';
        }

        try {
            $this->dqlParts['select'][] = $this->expr()->create(Select::class, func_get_args());
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
        $this->dqlParts['from'] = [];

        return $this->addFrom($spec, $alias, $options);
    }

    /**
     * addFrom
     *
     * Add a condition
     *
     * @param string $spec
     * @param string $alias
     * @param array  $options
     *
     * @return QueryBuilderInterface
     * @throws QueryBuilderException
     */
    public function addFrom(string $spec, string $alias, array $options) : QueryBuilderInterface
    {
        $indexBy = isset($options['index_by']) ? $options['index_by'] : null;

        try {
            $this->dqlParts['from'][] = $this->expr()->create(From::class, [$spec, $alias, $indexBy]);
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
        try {
            $this->dqlParts['join'][$alias] = $this->expr()->create(Join::class, [$type, $join, $alias, $conditions]);
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
        $this->dqlParts['where'] = [];

        return $this->andWhere($spec);
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