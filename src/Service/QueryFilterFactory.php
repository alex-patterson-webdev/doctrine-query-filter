<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\AndX;
use Arp\DoctrineQueryFilter\Equal;
use Arp\DoctrineQueryFilter\GreaterThan;
use Arp\DoctrineQueryFilter\GreaterThanOrEqual;
use Arp\DoctrineQueryFilter\In;
use Arp\DoctrineQueryFilter\IsNotNull;
use Arp\DoctrineQueryFilter\IsNull;
use Arp\DoctrineQueryFilter\LessThan;
use Arp\DoctrineQueryFilter\LessThanOrEqual;
use Arp\DoctrineQueryFilter\NotEqual;
use Arp\DoctrineQueryFilter\OrX;
use Arp\DoctrineQueryFilter\QueryFilterInterface;

/**
 * QueryFilterFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class QueryFilterFactory implements QueryFilterFactoryInterface
{
    /**
     * queryFilterManager
     *
     * @var QueryFilterManager
     */
    protected $queryFilterManager;

    /**
     * __construct
     *
     * @param QueryFilterManager $queryFilterManager
     */
    public function __construct(QueryFilterManager $queryFilterManager)
    {
        $this->queryFilterManager = $queryFilterManager;
    }

    /**
     * andX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return AndX
     */
    public function andX(...$spec)
    {
        return $this->create(AndX::class, $spec);
    }

    /**
     * orX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return OrX
     */
    public function orX(...$spec)
    {
        return $this->create(OrX::class, $spec);
    }

    /**
     * eq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return Equal
     */
    public function eq($a, $b)
    {
        return $this->create(Equal::class, func_get_args());
    }

    /**
     * neq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return NotEqual
     */
    public function neq($a, $b)
    {
        return $this->create(NotEqual::class, func_get_args());
    }

    /**
     * isNull
     *
     * @param string $fieldName
     * @param string $alias
     *
     * @return IsNull
     */
    public function isNull($fieldName, $alias = null)
    {
        return $this->create(IsNull::class, func_get_args());
    }

    /**
     * isNotNull
     *
     * @param string $fieldName
     * @param string $alias
     *
     * @return IsNotNull
     */
    public function isNotNull($fieldName, $alias = null)
    {
        return $this->create(IsNotNull::class, func_get_args());
    }

    /**
     * lt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThan
     */
    public function lt($a, $b)
    {
        return $this->create(LessThan::class, func_get_args());
    }

    /**
     * lte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThanOrEqual
     */
    public function lte($a, $b)
    {
        return $this->create(LessThanOrEqual::class, func_get_args());
    }

    /**
     * gt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThan
     */
    public function gt($a, $b)
    {
        return $this->create(GreaterThan::class, func_get_args());
    }

    /**
     * gte
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return GreaterThanOrEqual
     */
    public function gte($a, $b)
    {
        return $this->create(GreaterThanOrEqual::class, func_get_args());
    }

    /**
     * in
     *
     * @param string      $fieldName
     * @param array       $collection
     * @param string|null $alias
     *
     * @return In
     */
    public function in($fieldName, $collection, $alias = null)
    {
        return $this->create(In::class, func_get_args());
    }

    /**
     * create
     *
     * Create a new filter and seed it with the provided arguments.
     *
     * @param string  $name    The name of the query filter to create.
     * @param array   $args    The query filter's arguments.
     * @param array   $options The optional factory options.
     *
     * @return QueryFilterFactoryInterface
     */
    public function create($name, array $args = [], array $options = [])
    {
        $spec = [
            'config' => [
                'arguments' => $args,
                'options'   => $options
            ],
        ];

        return $this->queryFilterManager->build($name, $spec);
    }

}