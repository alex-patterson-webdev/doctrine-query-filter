<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\In;
use Arp\DoctrineQueryFilter\OrX;
use Arp\DoctrineQueryFilter\AndX;
use Arp\DoctrineQueryFilter\Equal;
use Arp\DoctrineQueryFilter\NotEqual;
use Arp\DoctrineQueryFilter\GreaterThan;
use Arp\DoctrineQueryFilter\GreaterThanOrEqual;
use Arp\DoctrineQueryFilter\LessThan;
use Arp\DoctrineQueryFilter\LessThanOrEqual;
use Arp\DoctrineQueryFilter\IsNull;
use Arp\DoctrineQueryFilter\IsNotNull;
use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryExpressionFactoryException;

/**
 * QueryExpressionFactoryInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryExpressionFactoryInterface
{
    /**
     * andX
     *
     * @param QueryExpressionInterface[] ...$spec
     *
     * @return AndX
     *
     * @throws QueryExpressionFactoryException
     */
    public function andX(...$spec) : AndX;

    /**
     * orX
     *
     * @param QueryExpressionInterface[] ...$spec
     *
     * @return OrX
     *
     * @throws QueryExpressionFactoryException
     */
    public function orX(...$spec) : OrX;

    /**
     * eq
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return Equal
     *
     * @throws QueryExpressionFactoryException
     */
    public function eq($a, $b) : Equal;

    /**
     * neq
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return NotEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function neq($a, $b) : NotEqual;

    /**
     * isNull
     *
     * @param string $fieldName
     *
     * @return IsNull
     *
     * @throws QueryExpressionFactoryException
     */
    public function isNull(string $fieldName) : IsNull;

    /**
     * isNotNull
     *
     * @param string $fieldName
     *
     * @return IsNotNull
     *
     * @throws QueryExpressionFactoryException
     */
    public function isNotNull(string $fieldName) : IsNotNull;

    /**
     * lt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThan
     *
     * @throws QueryExpressionFactoryException
     */
    public function lt($a, $b) : LessThan;

    /**
     * lte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThanOrEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function lte($a, $b) : LessThanOrEqual;

    /**
     * gt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThan
     *
     * @throws QueryExpressionFactoryException
     */
    public function gt($a, $b) : GreaterThan;

    /**
     * gte
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return GreaterThanOrEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function gte($a, $b) : GreaterThanOrEqual;

    /**
     * in
     *
     * @param string $fieldName
     * @param array  $collection
     *
     * @return In
     *
     * @throws QueryExpressionFactoryException
     */
    public function in(string $fieldName, array $collection) : In;

    /**
     * create
     *
     * Create a new filter and seed it with the provided arguments.
     *
     * @param mixed  $spec    The name of the query filter to create.
     * @param array  $args    The query filter's arguments.
     * @param array  $options The optional factory options.
     *
     * @return QueryExpressionInterface
     *
     * @throws QueryExpressionFactoryException
     */
    public function create($spec, array $args = [], array $options = []) : QueryExpressionInterface;

}