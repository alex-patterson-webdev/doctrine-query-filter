<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter as Filter;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterFactory;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * QueryFilterManager
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class QueryFilterManager extends AbstractPluginManager
{
    /**
     * $factories
     *
     * @var array
     */
    protected $factories = [
        Filter\AndX::class               => QueryFilterFactory::class,
        Filter\OrX::class                => QueryFilterFactory::class,
        Filter\Equal::class              => QueryFilterFactory::class,
        Filter\NotEqual::class           => QueryFilterFactory::class,
        Filter\IsNull::class             => QueryFilterFactory::class,
        Filter\IsNotNull::class          => QueryFilterFactory::class,
        Filter\GreaterThan::class        => QueryFilterFactory::class,
        Filter\GreaterThanOrEqual::class => QueryFilterFactory::class,
        Filter\LessThan::class           => QueryFilterFactory::class,
        Filter\LessThanOrEqual::class    => QueryFilterFactory::class,
        Filter\In::class                 => QueryFilterFactory::class,
        Filter\NotIn::class              => QueryFilterFactory::class,
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
        if ($queryFilter instanceof Filter\QueryFilterInterface) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'The query filter expression must be an object of type \'%s\'; \'%s\' provided in \'%s\'.',
            Filter\QueryFilterInterface::class,
            (is_object($queryFilter) ? get_class($queryFilter) : gettype($queryFilter)),
            __METHOD__
        ));
    }

}