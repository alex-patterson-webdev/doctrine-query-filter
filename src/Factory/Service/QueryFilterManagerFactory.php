<?php

namespace Arp\DoctrineQueryFilter\Factory\Service;

use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\Stdlib\Exception\ServiceNotCreatedException;
use Arp\Stdlib\Factory\AbstractServiceFactory;
use Interop\Container\ContainerInterface;

/**
 * QueryFilterManagerFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Factory\Service
 */
class QueryFilterManagerFactory extends AbstractServiceFactory
{
    /**
     * $defaultClassName
     *
     * @var string
     */
    protected $defaultClassName = QueryFilterManager::class;

    /**
     * create
     *
     * @param ContainerInterface $container     The dependency injection container.
     * @param string             $requestedName The name of the service requested to the container.
     * @param array              $config        The optional factory configuration options.
     * @param string|null        $className     The name of the class that is being created.
     *
     * @return QueryFilterManager
     *
     * @throws ServiceNotCreatedException  If the service cannot be created.
     */
    public function create(ContainerInterface $container, $requestedName, array $config = [], $className = null)
    {
        return new $className($container, $config);
    }

}