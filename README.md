[![Build Status](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter.svg?branch=master)](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter)
[![codecov](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter/branch/master/graph/badge.svg)](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/?branch=master)

# Doctrine Query Filter

## About

This package provides query filtering components for Doctrine ORM; converting array configuration into DQL queries. 

The project has been inspired by the [Laminas Doctrine QueryBuilder](https://github.com/laminas-api-tools/api-tools-doctrine-querybuilder); 
providing similar functionality without the Laminas Framework dependency.

## Installation

Installation via [composer](https://getcomposer.org).

    require alex-patterson-webdev/doctrine-query-filter ^0.3

## Query Filter Manager

All filtering is performed via the `Arp\DoctrineQueryFilter\QueryFilterManager`.

The `QueryFilterManager` requires an implementation of  `Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface` to 
create different types of filters. The `Arp\DoctrineQueryFilter\Filter\FilterFactory` can be used as a default implementation.
    
    use Arp\DoctrineQueryFilter\Filter\FilterFactory;
    use Arp\DoctrineQueryFilter\QueryFilterManager;
    
    $queryFilterManager = new QueryFilterManager(new FilterFactory());

The `QueryFilterManager` exposes a single public method, `QueryFilterManagerInterface::filter`. The `filter` method accepts
a `Doctring\ORM\QueryBuilder` instance to which it will apply filtering.

    // A Doctrine QueryBuilder instance for a customer query
    $queryBuilder = $entityManager->getRepository('Customer')->createQueryBuilder('c');

    // Apply the filters to the $queryBuilder
    $queryBuilder = $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

    // Fetch the constructed query and execute it
    $customers = $queryBuilder->getQuery()->execute();

## Query Criteria

### Query Filters

Query filters are objects that implement `Arp\DoctrineQueryFilter\Filter\QueryFilterInterface` and are used apply specific
filtering on the `$queryBuilder` passed to the `QueryFilterManager`.

For example, we can execute a query to find customers named `Fred`.

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'forename',
                'value' => 'Fred',
            ],
        ],
    ],
    $queryBuilder = $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

    // SELECT x FROM customer x WHERE x.forename = 'Fred' 
    $customers = $queryBuilder->getQuery()->execute();

Each filter defined must contain a `name` which is either the fully qualified class name of the filter, or an alias defined 
by the `FilterFactory`. 

### Filter Reference

There are many types of query filters, the table below defines the defaults available.

| Alias         | Class Name     | Description  | Required Options
| --------------|:-------------:| :-----:| :-----:
| eq    | Arp\DoctrineQueryFilter\Filter\IsEqual | Test is a = b | `field`, `value` |
| neq    | Arp\DoctrineQueryFilter\Filter\IsNotEqual | Test is a != b | `field`, `value` |
| gt    | Arp\DoctrineQueryFilter\Filter\IsGreaterThan | Test is a > b | `field`, `value` |
| gte    | Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual | Test is a >= b | `field`, `value` |
| lt    | Arp\DoctrineQueryFilter\Filter\IsLessThan | Test is a < b | `field`, `value` |
| lte    | Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual | Test is a <= b | `field`, `value` |
| andx    | Arp\DoctrineQueryFilter\Filter\AndX | Join two or more expressions using logical AND | `conditions` |
| orx    | Arp\DoctrineQueryFilter\Filter\OrX | Join two or more expressions using logical OR | `conditions` |
| between    | Arp\DoctrineQueryFilter\Filter\IsBetween | Test if a => min and a <= max | `field`, `min`, `max` |
| ismemberof    | Arp\DoctrineQueryFilter\Filter\IsMemberOf | Test if a exists within collection b | `field`, `value` |
| isnull    | Arp\DoctrineQueryFilter\Filter\IsNull | Test if a is NULL | `field` |
| isnotnull    | Arp\DoctrineQueryFilter\Filter\IsNotNull | Test if a is NOT NULL | `field` |
| islike    | Arp\DoctrineQueryFilter\Filter\IsLike | Test if a is LIKE b | `field`, `value` |
| isnotlike    | Arp\DoctrineQueryFilter\Filter\IsNotLike | Check if a is NOT LIKE b | `field`, `value` |
| isin    | Arp\DoctrineQueryFilter\Filter\IsIn | Check if a is IN b | `field`, `value` |
| isnotin    | Arp\DoctrineQueryFilter\Filter\IsNotIn | Check if a is NOT IN b | `field`, `value` |

### Composing filters

The true power of the library is the ability to nest and compose multiple query filters together to further filter a collection.

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'forename',
                'value' => 'Fred',
            ],
            [
                'name' => 'eq'
                'field' => 'gender',
                'value' => 'Male',
            ],
        ],
    ],

When defining more that one filter, conditions will be explicitly `AND` together useing the `AndX` composite query filter. 
To instead perform an `OR` condition we must define a `orx` filter and provide it with the required `conditions` array.

    $criteria = [
        'filters' => [
            [
                'name' => 'orx',
                'conditions' => [
                    [
                        'name' => 'eq',
                        'field' => 'surname',
                        'value' => 'Smith',
                    ],
                    [
                        'name' => 'eq'
                        'field' => 'gender',
                        'value' => 'Male',
                    ],
                ]
            ]
        ],
    ];

You can also nest a combination of the `andX` and `orX`, the generated DQL will include the correct grouping.

    // WHERE x.surname = 'Smith' OR (x.age > 18 AND x.gender = 'Male')
    $criteria = [
        'filters' => [
            [
                'name' => 'or',
                'conditions' => [
                    [
                        'name' => 'eq',
                        'field' => 'surname',
                        'value' => 'Smith',
                    ],
                    [
                        'name' => 'andx',
                        'conditions' => [
                            [
                                'name' => 'gt',
                                'field' => 'age',
                                'value' => 18,
                            ],
                            [
                                'name' => 'eq'
                                'field' => 'gender',
                                'value' => 'Male',
                            ],
                        ]
                    ],
                ]
            ]
        ],
    ];
    
## Filtering examples

@todo

## FilterFactory

If you require greater control on the construction of the query filters, it is possible to provide `QueryFilter` 
instances directly to the `$criteria['filters']` array.

For example

    $filterFactory = new FilterFactory();
    $queryFilterManager = new QueryFilterManager($filterFactory);
    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'surname',
                'value' => 'Smith',
            ],
            $filterFactory->create('between', ['field' => 'age', 'from => 18, 'to' => 65]),
        ],
    ],



## Unit tests

Unit tests can be executed using PHPUnit from the application root directory.

    php vendor/bin/phpunit
