<?php

namespace Arp\DoctrineQueryFilter\Factory\Service;

use Arp\DoctrineQueryFilter\Service\QueryExpressionFactory;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactoryInterface;
use Arp\DoctrineQueryFilter\Service\QueryExpressionManager;
use Arp\Stdlib\Exception\ServiceNotCreatedException;
use Arp\Stdlib\Factory\AbstractServiceFactory;
use Interop\Container\ContainerInterface;

/**
 * QueryExpressionFactoryFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Factory\Service
 */
class QueryExpressionFactoryFactory extends AbstractServiceFactory
{
    /**
     * $defaultClassName
     *
     * @var string
     */
    protected $defaultClassName = QueryExpressionFactory::class;

    /**
     * create
     *
     * @param ContainerInterface $container     The dependency injection container.
     * @param string             $requestedName The name of the service requested to the container.
     * @param array              $config        The optional factory configuration options.
     * @param string|null        $className     The name of the class that is being created.
     *
     * @return QueryExpressionFactoryInterface
     *
     * @throws ServiceNotCreatedException  If the service cannot be created.
     */
    public function create(ContainerInterface $container, $requestedName, array $config = [], $className = null)
    {
        return new $className(
            $container->get(QueryExpressionManager::class)
        );
    }

}