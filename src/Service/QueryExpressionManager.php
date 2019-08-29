<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter as Filter;
use Arp\DoctrineQueryFilter\Factory\Service\QueryExpressionFactory;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * QueryExpressionManager
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class QueryExpressionManager extends AbstractPluginManager
{
    /**
     * $factories
     *
     * @var array
     */
    protected $factories = [
        Filter\AndX::class               => QueryExpressionFactory::class,
        Filter\OrX::class                => QueryExpressionFactory::class,
        Filter\Equal::class              => QueryExpressionFactory::class,
        Filter\NotEqual::class           => QueryExpressionFactory::class,
        Filter\IsNull::class             => QueryExpressionFactory::class,
        Filter\IsNotNull::class          => QueryExpressionFactory::class,
        Filter\GreaterThan::class        => QueryExpressionFactory::class,
        Filter\GreaterThanOrEqual::class => QueryExpressionFactory::class,
        Filter\LessThan::class           => QueryExpressionFactory::class,
        Filter\LessThanOrEqual::class    => QueryExpressionFactory::class,
        Filter\In::class                 => QueryExpressionFactory::class,
        Filter\NotIn::class              => QueryExpressionFactory::class,
    ];

    /**
     * validate
     *
     * @param mixed  $queryFilter
     *
     * @throws InvalidServiceException
     */
    public function validate($queryFilter)
    {
        if ($queryFilter instanceof Filter\QueryExpressionInterface) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'The query filter expression must be an object of type \'%s\'; \'%s\' provided in \'%s\'.',
            Filter\QueryExpressionInterface::class,
            (is_object($queryFilter) ? get_class($queryFilter) : gettype($queryFilter)),
            __METHOD__
        ));
    }

}