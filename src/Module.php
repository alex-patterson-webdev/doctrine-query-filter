<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\DoctrineQueryFilter\Service\QueryFilterManagerProviderInterface;
use Zend\ModuleManager\Listener\ServiceListenerInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\ServiceManager;

/**
 * Module
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Module
{
    /**
     * init
     *
     * @param ModuleManagerInterface|ModuleManager $moduleManager
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $moduleManager->getEvent()->getParam('ServiceManager');

        /** @var ServiceListenerInterface $serviceListener */
        $serviceListener = $serviceManager->get('ServiceListener');

        $serviceListener->addServiceManager(
            QueryFilterManager::class,
            'query_filter_manager',
            QueryFilterManagerProviderInterface::class,
            'getQueryFilterConfig'
        );
    }

    /**
     * getConfig
     *
     * Return the module configuration array.
     *
     * @return array
     */
    public function getConfig()
    {
        return require __DIR__ . '/../config/module.config.php';
    }
}