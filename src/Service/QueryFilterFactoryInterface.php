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
     */
    public function andX(...$spec);

    /**
     * orX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return OrX
     */
    public function orX(...$spec);

    /**
     * eq
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return Equal
     */
    public function eq($a, $b);

    /**
     * neq
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return NotEqual
     */
    public function neq($a, $b);

    /**
     * isNull
     *
     * @param string $fieldName
     * @param string $alias
     *
     * @return IsNull
     */
    public function isNull($fieldName, $alias = null);

    /**
     * isNotNull
     *
     * @param string $fieldName
     * @param string $alias
     *
     * @return IsNotNull
     */
    public function isNotNull($fieldName, $alias = null);

    /**
     * lt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThan
     */
    public function lt($a, $b);

    /**
     * lte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThanOrEqual
     */
    public function lte($a, $b);

    /**
     * gt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThan
     */
    public function gt($a, $b);

    /**
     * gte
     *
     * @param mixed  $a
     * @param mixed  $b
     *
     * @return GreaterThanOrEqual
     */
    public function gte($a, $b);

    /**
     * in
     *
     * @param string      $fieldName
     * @param array       $collection
     * @param string|null $alias
     *
     * @return In
     */
    public function in($fieldName, $collection, $alias = null);

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
     */
    public function create($name, array $args = [], array $options = []);

}