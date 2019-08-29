<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\DoctrineQueryFilter\Service\QueryExpressionFactory;
use Arp\DoctrineQueryFilter\Service\QueryExpressionManager;
use Arp\DoctrineQueryFilter\Factory\Service\QueryExpressionFactoryFactory;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterManagerFactory;

return [
    'query_filter_manager' => [
        'factories' => [],
    ],
    'service_manager' => [
        'factories' => [
            QueryFilterManager::class     => QueryFilterManagerFactory::class,
            QueryExpressionManager::class => QueryFilterManagerFactory::class,
            QueryExpressionFactory::class => QueryExpressionFactoryFactory::class,
        ]
    ],
];