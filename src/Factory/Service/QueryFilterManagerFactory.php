<?php

namespace Arp\DoctrineQueryFilter\Factory\Service;

use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Zend\Mvc\Service\AbstractPluginManagerFactory;

/**
 * QueryFilterManagerFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Factory\Service
 */
class QueryFilterManagerFactory extends AbstractPluginManagerFactory
{
    /**
     * @const
     */
    const PLUGIN_MANAGER_CLASS = QueryFilterManager::class;

}