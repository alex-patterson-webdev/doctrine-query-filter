<?php

namespace Arp\DoctrineQueryFilter\Service;

/**
 * QueryFilterManagerProviderInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryFilterManagerProviderInterface
{
    /**
     * getQueryFilterConfig
     *
     * @return array
     */
    public function getQueryFilterConfig();
}