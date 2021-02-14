[![Build Status](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter.svg?branch=master)](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter)
[![codecov](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter/branch/master/graph/badge.svg)](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/?branch=master)

# Doctrine Query Filter

## About

Provides Query filtering components for the Doctrine ORM QueryBuilder using `array` parameters.

This project has been inspired by the [Laminas Doctrine QueryBuilder](https://github.com/laminas-api-tools/api-tools-doctrine-querybuilder) project.

## Installation

Installation via [composer](https://getcomposer.org).

    require alex-patterson-webdev/doctrine-query-filter ^0.1

## Documentation

### Query Filter Manager

In order to filter Doctrine entities, we must first create a `QueryFilterManager` instance. The `QueryFilterManager` 
has a single dependency `Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface`. You can use the default
`Arp\DoctrineQueryFilter\Filter\FilterFactory` implementation to get started.

    use Arp\DoctrineQueryFilter\Filter\FilterFactory;
    use Arp\DoctrineQueryFilter\QueryFilterManager;
    
    $queryFilterManager = new QueryFilterManager(new FilterFactory());

The `QueryFilterManager` exposes two public methods.

    interface QueryFilterManagerInterface
    {
        public function filter($queryBuilder, string $entityName, array $criteria): QueryBuilderInterface;

        public function createFilter(string $name, array $options = []): FilterInterface;
    }

### Query Filters

We construct an array of `Arp\DoctrineQueryFilter\Filter\FilterInterface` instances and apply filtering on a `Doctrine\ORM\QueryBuilder` 
instance passed to `QueryFilterManager::filter()`. 

We can pass a simple array of filter criteria to allow the `QueryFilterManager` to construct the required query filters for use.

    $queryFilterManager = new QueryFilterManager(new FilterFactory());

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'first_name',
                'value' => 'Fred',
            ],
            [
                'name' => 'eq',
                'field' => 'surname',
                'value' => 'Smith',
            ],
            
        ],
    ],

    $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

Additionally, we can also achieve the same result using the `QueryFilterManager::createFilter()` method directly creating 
the query filters for greater control.

    $queryFilterManager = new QueryFilterManager(new FilterFactory());

    $criteria = [
        'filters' => [
            $queryFilterManager->createFilter('eq', ['field' => 'first_name', 'value' => 'Fred']),
            $queryFilterManager->createFilter('eq', ['field' => 'surname', 'value' => 'Smith']),
        ],
    ],

    $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

There are many query filters that can be used, each can be referenced by their alias or fully qualified class name.

| Alias         | Class Name     | Description  |
| --------------|:-------------:| -----:|
| eq    | Arp\DoctrineQueryFilter\Filter\IsEqual | Test is A = B |
| neq    | Arp\DoctrineQueryFilter\Filter\IsNotEqual | Test is A != B |
| gt    | Arp\DoctrineQueryFilter\Filter\IsGreaterThan | Test is A > B |
| gte    | Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual | Test is A >= B |
| lt    | Arp\DoctrineQueryFilter\Filter\IsLessThan | Test is A < B |
| lte    | Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual | Test is A <= B |
| andx    | Arp\DoctrineQueryFilter\Filter\AndX | Join two or more expressions using logical AND |
| orx    | Arp\DoctrineQueryFilter\Filter\AndX | Join two or more expressions using logical OR |
| ismemberof    | Arp\DoctrineQueryFilter\Filter\IsMemberOf | Check if x exists within collection y |
| isnull    | Arp\DoctrineQueryFilter\Filter\IsNull | Check if A is NULL |
| isnotnull    | Arp\DoctrineQueryFilter\Filter\IsNotNull | Check if B is NOT NULL |

