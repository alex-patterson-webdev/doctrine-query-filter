[![Build Status](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter.svg?branch=master)](https://travis-ci.com/alex-patterson-webdev/doctrine-query-filter)
[![codecov](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter/branch/master/graph/badge.svg)](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/?branch=master)

# Doctrine Query Filter

## About

The package provides Query filtering components for the Doctrine ORM QueryBuilder.

This project has been inspired by the [Laminas Doctrine QueryBuilder](https://github.com/laminas-api-tools/api-tools-doctrine-querybuilder) project
and provides similar functionality without the Laminas Framework dependency.

## Theory and Use case

When creating API's, developers often need to fetch resources from endpoints that allow for filtering of arbitrary criteria. 
A simple example could be where a `Customer` endpoint allows query parameters to find `customer` resources that 
match a given `forename`, `surname` and `age` range.

Such a request could look like this

    GET /api/v1/customers?forename=Fred&surname=Smith&age_min=18&age_max=65`

When using the Doctrine ORM query builder to handle the API's request we must resolve these query parameters and 
manually to construct a query.

    $queryBuilder->where(
        'c.forename = :forename AND c.surname = :surname AND age >= :age_min AND age <= :age_max'
    )
    ->setParameter('forename', $_GET['forename'])
    ->setParameter('surname', $_GET['surname'])
    ->setParameter('age_min', $_GET['age_min'])
    ->setParameter('age_max', $_GET['age_max']);

This package provides a generic structure to define query filter criteria when passing parameters via the URL. Essentially we group the
filter requirements into query filter criteria, much like the below

    GET /api/v1/customers
        ?filters[0][name]=eq&filters[0][field]=forename&filters[0][value]=Fred
        &filters[1][name]=eq&filters[1][field]=surname&filters[1][value]=Smith
        &filters[2][name]=between&filters[2][field]=age&filters[2][min]=18&filters[2][max]=65

The request parameters would resolve as a `array` like below. Each filter requires at a minimum a `name`
which will determine the type of filtering to create and then apply.

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'forename',
                'value' => 'Fred',
            ],
            [
                'name' => 'eq',
                'field' => 'surname',
                'value' => 'Smith',
            ],
            [
                'name' => 'between',
                'field' => 'age',
                'min' => 18,
                'max' => 65,
            ],
        ],
    ],

This approach provides a number of benefits:

- Provides an intuitive and consistent API for query filtering that can be used for all of your endpoints.
- No need to create any doctrine queries as they can now be created directly from the filter criteria.  
- We can implement query filtering logic in a single place for all entities/resources.
- Complex query filters can be created by _nesting_ query filter components.
- Dynamically validate the query filters fields using Doctrine metadata to prevent filtering on invalid fields.

## Installation

Installation via [composer](https://getcomposer.org).

    require alex-patterson-webdev/doctrine-query-filter ^0.2

## Documentation

### Query Filter Manager

We require a new `Arp\DoctrineQueryFilter\QueryFilterManager` instance in order to apply query filters to a Doctrine QueryBuilder instance. 
The `QueryFilterManager` has a single dependency of a `Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface`. The 
FilterFactory allows the QueryFilterManager to internally resolve and create filters we want to apply in an abstract way.

You can use the default `Arp\DoctrineQueryFilter\Filter\FilterFactory` to get started; or if required this can be  
swapped for a custom implementation of `FilterFactoryInterface`.

    use Arp\DoctrineQueryFilter\Filter\FilterFactory;
    use Arp\DoctrineQueryFilter\QueryFilterManager;
    
    $queryFilterManager = new QueryFilterManager(new FilterFactory());

The `QueryFilterManager` exposes a single public method, `QueryFilterManagerInterface::filter`. 

    use Arp\DoctrineQueryFilter\QueryBuilderInterface;
    use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

    interface QueryFilterManagerInterface
    {
        public function filter($queryBuilder, string $entityName, array $criteria): DoctrineQueryBuilder;
    }

The `filter()` method will accept a query builder instance and query filter criteria. The query filters closely match
existing criteria offered by `Doctrine\ORM\QueryBuilder`, however we are able to provide the criteria as array configuration. 
Internally the query filter manager will work out what filters should be applied to the provided `$queryBuilder`.

For example, we can create a query to fetch customers named "Fred Smith" using the following criteria. The `eq` query filter
will map to Doctrine's `Doctrine\ORM\Query\Expr\Comparison::EQ`.

> When adding more than one query filter, the conditions will be explicitly `AND` together. If you wish to construct a 
> logical `OR` please use the `Orx` query filter documented below.

    $queryFilterManager = new QueryFilterManager(new FilterFactory());
    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'forename',
                'value' => 'Fred',
            ],
            [
                'name' => 'eq',
                'field' => 'surname',
                'value' => 'Smith',
            ],
            [
                'name' => 'between',
                'field' => 'age',
                'min' => 18,
                'max' => 65,
            ],
        ],
    ],

    // @var \Doctrine\ORM\QueryBuilder $queryBuilder
    $queryBuilder = $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);
    $customers = $queryBuilder->getQuery()->execute();

If you require greater control on the construction of the query filters, it is possible to provide `QueryFilter` instances directly
to the `$criteria['filters']` array.

The example below is equivalent

    $filterFactory = new FilterFactory();
    $queryFilterManager = new QueryFilterManager($filterFactory);
    $criteria = [
        'filters' => [
            $filterFactory->create('eq', ['field' => 'forename', 'value' => 'Fred']),
            [
                'name' => 'eq',
                'field' => 'surname',
                'value' => 'Smith',
            ],
            $filterFactory->create('between', ['field' => 'age', 'min' => 18, 'max' => 65]),
        ],
    ],

### Query Filters 

There are many query filters that can be used, each can be referenced by an alias, or alternatively fully qualified class name.
The alias or name must be provided for all filters with key `name`.


| Alias         | Class Name     | Description  | Required Options
| --------------|:-------------:| :-----:| :-----:
| eq    | Arp\DoctrineQueryFilter\Filter\IsEqual | Test is A = B | `field`, `value` |
| neq    | Arp\DoctrineQueryFilter\Filter\IsNotEqual | Test is A != B | `field`, `value` |
| gt    | Arp\DoctrineQueryFilter\Filter\IsGreaterThan | Test is A > B | `field`, `value` |
| gte    | Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual | Test is A >= B | `field`, `value` |
| lt    | Arp\DoctrineQueryFilter\Filter\IsLessThan | Test is A < B | `field`, `value` |
| lte    | Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual | Test is A <= B | `field`, `value` |
| andx    | Arp\DoctrineQueryFilter\Filter\AndX | Join two or more expressions using logical AND | `conditions` |
| orx    | Arp\DoctrineQueryFilter\Filter\AndX | Join two or more expressions using logical OR | `conditions` |
| between    | Arp\DoctrineQueryFilter\Filter\IsBetween | Test if A => min and A <= max | `field`, `min`, `max` |
| ismemberof    | Arp\DoctrineQueryFilter\Filter\IsMemberOf | Check if x exists within collection y | `field`, `value` |
| isnull    | Arp\DoctrineQueryFilter\Filter\IsNull | Check if A is NULL | `field` |
| isnotnull    | Arp\DoctrineQueryFilter\Filter\IsNotNull | Check if B is NOT NULL | `field` |

### Composite Query Filters

The composite query filters are designed to allow for other query filters
to be nested together. Filters will explicitly use the `AndX` composite when passed to `filter()`.
To join filters using an OR condition we must nest it within a `OrX` filter.

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
                        'name' => 'eq',
                        'field' => 'surname',
                        'value' => 'Doe',
                    ],
                ]
            ]
        ],
    ];

This is equivalent to a `(surname = 'Smith' OR surname = 'Doe')` DQL condition.

## Unit tests

Unit tests can be executed using PHPUnit from the application root directory.

    php vendor/bin/phpunit
