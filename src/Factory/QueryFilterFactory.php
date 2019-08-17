<?php

namespace Arp\DoctrineQueryFilter\Factory;

use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\Stdlib\Factory\AbstractFactory;
use Arp\Stdlib\Exception\ServiceNotCreatedException;
use Interop\Container\ContainerInterface;

/**
 * QueryFilterFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Factory
 */
class QueryFilterFactory extends AbstractFactory
{
    /**
     * create
     *
     * @param ContainerInterface $container     The dependency injection container.
     * @param string             $requestedName The name of the service requested to the container.
     * @param array              $config        The optional factory configuration options.
     * @param string|null        $className     The name of the class that is being created.
     *
     * @return QueryFilterInterface
     *
     * @throws ServiceNotCreatedException  If the service cannot be created.
     */
    public function create(ContainerInterface $container, $requestedName, array $config = [], $className = null)
    {
        $className = isset($className) ?: $requestedName;
        $arguments = isset($config['arguments']) ? $config['arguments'] : [];

        return new $className(...$arguments);
    }

}