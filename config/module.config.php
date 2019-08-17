<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
use Arp\DoctrineQueryFilter\Service\QueryFilterManager;
use Arp\DoctrineQueryFilter\Factory\QueryFilterFactory as FilterFactory;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterFactoryFactory;
use Arp\DoctrineQueryFilter\Factory\Service\QueryFilterManagerFactory;

return [
    'query_filter_manager' => [
        'factories' => [
            AndX::class               => FilterFactory::class,
            OrX::class                => FilterFactory::class,
            Equal::class              => FilterFactory::class,
            NotEqual::class           => FilterFactory::class,
            IsNull::class             => FilterFactory::class,
            IsNotNull::class          => FilterFactory::class,
            GreaterThan::class        => FilterFactory::class,
            GreaterThanOrEqual::class => FilterFactory::class,
            LessThan::class           => FilterFactory::class,
            LessThanOrEqual::class    => FilterFactory::class,
            In::class                 => FilterFactory::class,
            NotIn::class              => FilterFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            QueryFilterManager::class => QueryFilterManagerFactory::class,
            QueryFilterFactory::class => QueryFilterFactoryFactory::class,
        ]
    ],
];