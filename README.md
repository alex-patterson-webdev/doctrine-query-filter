![build](https://github.com/alex-patterson-webdev/doctrine-query-filter/actions/workflows/workflow.yml/badge.svg)
[![codecov](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter/branch/master/graph/badge.svg)](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/?branch=master)

# Doctrine Query Filter

## About

This package provides query filtering components for Doctrine ORM. By modeling query filter criteria as reusable objects, 
it offers a consistent and extendable way of constructing complex DQL statements.

The project has been inspired by the [Laminas Doctrine QueryBuilder](https://github.com/laminas-api-tools/api-tools-doctrine-querybuilder); 
providing similar functionality without the Laminas Framework dependency. 

## Installation

Installation via [composer](https://getcomposer.org).

    require alex-patterson-webdev/doctrine-query-filter ^0.7

## Usage

Using the `QueryFilterManager` we can create DQL strings from `array` format. The query filter manager will take care of binding the required parameter values.

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'name',
                'value' => 'Fred',
            ],
            [
                'name' => 'between',
                'field' => 'age',
                'min' => 18,
                'max' => 30
            ],
        ],
    ];

    $queryFilterManager = new QueryFilterManager(new FilterFactory());

    // A Doctrine QueryBuilder instance for a customer
    $queryBuilder = $entityManager->getRepository('Customer')->createQueryBuilder('c');

    // Apply the filters to the $queryBuilder
    $queryBuilder = $queryFilterManager->filter(
        $queryBuilder,
        Customer::class,
        $criteria
    );

    $query = $queryBuilder->getQuery();

    // SELECT c FROM customer c WHERE c.forename = :name AND (c.age >= :age_min AND c.age <= :age_max)
    echo $query->getDQL();

    $customers = $query->execute();

### Composing Filters

When defining more than one filter, conditions will be explicitly `AND` together using the `AndX` composite query filter.
To instead create an `OR` condition, we must define a `orx` filter and provide it with the required `conditions` array.

    // SELECT u FROM users u WHERE u.active = :active AND (u.username >= :username1 OR u.username <= :username2)
    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'enabled',
                'value' => true,
            ],
            [
                'name' => 'orx',
                'conditions' => [
                    [
                        'name' => 'eq',
                        'field' => 'username',
                        'value' => 'Fred',
                    ],
                    [
                        'name' => 'eq',
                        'field' => 'username',
                        'value' => 'bob',
                    ],
                ]
            ],
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


### Filter Reference

There are many types of query filters, the table below defines the defaults available.

| Alias      |                     Class Name                      |                  Description                   |   Required Options    |
|------------|:---------------------------------------------------:|:----------------------------------------------:|:---------------------:|
| eq         |       Arp\DoctrineQueryFilter\Filter\IsEqual        |                 Test is a = b                  |   `field`, `value`    |
| neq        |      Arp\DoctrineQueryFilter\Filter\IsNotEqual      |                 Test is a != b                 |   `field`, `value`    |
| gt         |    Arp\DoctrineQueryFilter\Filter\IsGreaterThan     |                 Test is a > b                  |   `field`, `value`    |
| gte        | Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual |                 Test is a >= b                 |   `field`, `value`    |
| lt         |      Arp\DoctrineQueryFilter\Filter\IsLessThan      |                 Test is a < b                  |   `field`, `value`    |
| lte        |  Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual   |                 Test is a <= b                 |   `field`, `value`    |
| andx       |         Arp\DoctrineQueryFilter\Filter\AndX         | Join two or more expressions using logical AND |     `conditions`      |
| orx        |         Arp\DoctrineQueryFilter\Filter\OrX          | Join two or more expressions using logical OR  |     `conditions`      |
| between    |      Arp\DoctrineQueryFilter\Filter\IsBetween       |         Test if a => min and a <= max          | `field`, `from`, `to` |
| ismemberof |      Arp\DoctrineQueryFilter\Filter\IsMemberOf      |      Test if a exists within collection b      |   `field`, `value`    |
| isnull     |        Arp\DoctrineQueryFilter\Filter\IsNull        |               Test if a is NULL                |        `field`        |
| isnotnull  |      Arp\DoctrineQueryFilter\Filter\IsNotNull       |             Test if a is NOT NULL              |        `field`        |
| islike     |        Arp\DoctrineQueryFilter\Filter\IsLike        |              Test if a is LIKE b               |   `field`, `value`    |
| isnotlike  |      Arp\DoctrineQueryFilter\Filter\IsNotLike       |            Check if a is NOT LIKE b            |   `field`, `value`    |
| isin       |         Arp\DoctrineQueryFilter\Filter\IsIn         |               Check if a is IN b               |   `field`, `value`    |
| isnotin    |       Arp\DoctrineQueryFilter\Filter\IsNotIn        |             Check if a is NOT IN b             |   `field`, `value`    |


## FilterFactory

If you require greater control on the construction of the query filters, it is possible to provide `QueryFilter` 
instances directly to the `$criteria['filters']` array instead of using the array format.

    $filterFactory = new FilterFactory();
    $queryFilterManager = new QueryFilterManager($filterFactory);
    $criteria = [
        'filters' => [
            $filterFactory->create('eq', ['field' => 'surname', 'value => 'Smith']),
            $filterFactory->create('between', ['field' => 'age', 'from => 18, 'to' => 65]),
        ],
    ],

## Sorting Results

In addition to filtering collections, we can also define how they should be sorted by using the `sort` criteria key. 
Each sort filter requires a `field` and `direction` key.

    $criteria = [
        'filters' => [
            //....
        ],
        'sort' => [
            [
                'field' => 'age',
                'direction' => SortDirection::DESC,
            ],
            [
                'field' => 'createdDate',
                'direction' => SortDirection::ASC,
            ]
        ],
    ];

## Unit tests

Unit tests can be executed using PHPUnit from the application root directory.

    php vendor/bin/phpunit
