<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\QueryFilterInterface;
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
     * validate
     *
     * @param mixed  $queryFilter
     *
     * @throws InvalidServiceException
     */
    public function validate($queryFilter)
    {
        if ($queryFilter instanceof QueryFilterInterface) {
            return;
        }

        throw new InvalidServiceException(sprintf(
            'The query filter service must be an object of type \'%s\'; \'%s\' provided in \'%s\'.',
            QueryFilterInterface::class,
            (is_object($queryFilter) ? get_class($queryFilter) : gettype($queryFilter)),
            __METHOD__
        ));
    }

}