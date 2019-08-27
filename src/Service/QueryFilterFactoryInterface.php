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
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterFactoryException;

/**
 * QueryFilterFactoryInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryFilterFactoryInterface
{
    /**
     * andX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return AndX
     *
     * @throws QueryFilterFactoryException
     */
    public function andX(...$spec) : AndX;

    /**
     * orX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return OrX
     *
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
     */
    public function neq($a, $b) : NotEqual;

    /**
     * isNull
     *
     * @param string $fieldName
     *
     * @return IsNull
     *
     * @throws QueryFilterFactoryException
     */
    public function isNull(string $fieldName) : IsNull;

    /**
     * isNotNull
     *
     * @param string $fieldName
     *
     * @return IsNotNull
     *
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
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
     * @throws QueryFilterFactoryException
     */
    public function in(string $fieldName, array $collection) : In;

    /**
     * create
     *
     * Create a new filter and seed it with the provided arguments.
     *
     * @param string  $name    The name of the query filter to create.
     * @param array   $args    The query filter's arguments.
     * @param array   $options The optional factory options.
     *
     * @return QueryFilterInterface
     *
     * @throws QueryFilterFactoryException
     */
    public function create(string $name, array $args = [], array $options = []) : QueryFilterInterface;

}