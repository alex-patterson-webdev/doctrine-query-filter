<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterFactoryFactory;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterManagerFactory;

return [
    'query_filter_manager' => [
        'factories' => [],
    ],
    'service_manager' => [
        'factories' => [
            QueryFilterManager::class     => QueryFilterManagerFactory::class,
            QueryFilterManager::class => QueryFilterManagerFactory::class,
            QueryFilterFactory::class => QueryFilterFactoryFactory::class,
        ]
    ],
];