<?php

namespace Arp\DoctrineQueryFilter\Factory\Service;

use Arp\DoctrineQueryFilter\QueryExpressionInterface;
use Arp\Stdlib\Exception\ServiceNotCreatedException;
use Arp\Stdlib\Factory\AbstractFactory;
use Interop\Container\ContainerInterface;

/**
 * QueryExpressionFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Factory\Service
 */
class QueryExpressionFactory extends AbstractFactory
{
    /**
     * create
     *
     * @param ContainerInterface $container     The dependency injection container.
     * @param string             $requestedName The name of the service requested to the container.
     * @param array              $config        The optional factory configuration options.
     * @param string|null        $className     The name of the class that is being created.
     *
     * @return QueryExpressionInterface
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