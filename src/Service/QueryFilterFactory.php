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
use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterFactoryException;

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
     *
     * @throws QueryFilterFactoryException
     */
    public function andX(...$spec) : AndX
    {
        /** @var AndX $queryFilter */
        $queryFilter = $this->create(AndX::class, $spec);

        return$queryFilter;
    }

    /**
     * orX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return OrX
     *
     * @throws QueryFilterFactoryException
     */
    public function orX(...$spec) : OrX
    {
        /** @var OrX $queryFilter */
        $queryFilter = $this->create(OrX::class, $spec);

        return$queryFilter;
    }

    /**
     * eq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return Equal
     *
     * @throws QueryFilterFactoryException
     */
    public function eq($a, $b) : Equal
    {
        /** @var Equal $queryFilter */
        $queryFilter = $this->create(Equal::class, func_get_args());

        return $queryFilter;
    }

    /**
     * neq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return NotEqual
     *
     * @throws QueryFilterFactoryException
     */
    public function neq($a, $b) : NotEqual
    {
        /** @var NotEqual $queryFilter */
        $queryFilter = $this->create(NotEqual::class, func_get_args());

        return $queryFilter;
    }

    /**
     * isNull
     *
     * @param string $fieldName
     *
     * @return IsNull
     *
     * @throws QueryFilterFactoryException
     */
    public function isNull(string $fieldName) : IsNull
    {
        /** @var IsNull $queryFilter */
        $queryFilter = $this->create(IsNull::class, func_get_args());

        return $queryFilter;
    }

    /**
     * isNotNull
     *
     * @param string $fieldName
     *
     * @return IsNotNull
     *
     * @throws QueryFilterFactoryException
     */
    public function isNotNull(string $fieldName) : IsNotNull
    {
        /** @var IsNotNull $queryFilter */
        $queryFilter = $this->create(IsNotNull::class, func_get_args());

        return $queryFilter;
    }

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
    public function lt($a, $b) : LessThan
    {
        /** @var LessThan $queryFilter */
        $queryFilter = $this->create(LessThan::class, func_get_args());

        return $queryFilter;
    }

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
    public function lte($a, $b) : LessThanOrEqual
    {
        /** @var LessThanOrEqual $queryFilter */
        $queryFilter = $this->create(LessThanOrEqual::class, func_get_args());

        return $queryFilter;
    }

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
    public function gt($a, $b) : GreaterThan
    {
        /** @var GreaterThan $queryFilter */
        $queryFilter = $this->create(GreaterThan::class, func_get_args());

        return $queryFilter;
    }

    /**
     * gte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThanOrEqual
     *
     * @throws QueryFilterFactoryException
     */
    public function gte($a, $b) : GreaterThanOrEqual
    {
        /** @var GreaterThanOrEqual $queryFilter */
        $queryFilter = $this->create(GreaterThanOrEqual::class, func_get_args());

        return $queryFilter;
    }

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
    public function in(string $fieldName, $collection) : In
    {
        /** @var In $queryFilter */
        $queryFilter = $this->create(In::class, func_get_args());

        return $queryFilter;
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
     * @return QueryFilterInterface
     *
     * @throws QueryFilterFactoryException
     */
    public function create(string $name, array $args = [], array $options = []) : QueryFilterInterface
    {
        $spec = [
            'config' => [
                'arguments' => $args,
                'options'   => $options
            ],
        ];

        try {
            return $this->queryFilterManager->build($name, $spec);
        }
        catch(\Exception $e) {

            throw new QueryFilterFactoryException(
                sprintf(
                    'Unable to create new query filter \'%s\' : %s',
                    $name,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

}