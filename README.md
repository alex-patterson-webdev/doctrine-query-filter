![build](https://github.com/alex-patterson-webdev/doctrine-query-filter/actions/workflows/workflow.yml/badge.svg)
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

## Doctrine\ORM\EntityRepository

We can add the filtering functionality to any class that has access to a query builder instance using the `FilterServiceInterface`. 
Ideally this would be inside a class that extends from `Doctrine\ORM\EntityRepository`.

For example

    use Arp\DoctrineQueryFilter\FilterServiceInterface;
    use Doctrine\ORM\EntityReposiotry;
    use User\Entity\User;

    class UserRepository extends EntityRepository implements FilterServiceInterface
    {
        public function filter(QueryFilterManagerInterface $filterManager, array $criteria, array $options = []): iterable
        {
            $queryBuilder = $filterManager->filter(
                $this->createQueryBuilder('u'),
                User::class,
                $criteria
            );
    
            return $queryBuilder->getQuery()->execute();
        }
    }

We can then use the new repository `filter()` method by passing in the required `QueryFilterManager` and `$criteria`.

    $criteria = [
        'filters' => [
            //...filtering criteria
        ]
    ];
    $users = $entityManager->getRepository('User')->filter($filterManager, $criteria);

## Query Criteria

### Query Filters

Query filters are objects that implement `Arp\DoctrineQueryFilter\Filter\QueryFilterInterface` and are used to apply specific
filtering on the `$queryBuilder` passed to the `QueryFilterManager`.

To avoid the need to construct many query filter objects, we can define our filter criteria using array configuration. 
The options required for each filter will depend on type of filtering being performed. All filters require  
a `name` option, which denotes the filter that should be created.

For example, we can define a `eq` filter to find customers who have a forename equal to `Fred`.

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
| between    | Arp\DoctrineQueryFilter\Filter\IsBetween | Test if a => min and a <= max | `field`, `from`, `to` |
| ismemberof    | Arp\DoctrineQueryFilter\Filter\IsMemberOf | Test if a exists within collection b | `field`, `value` |
| isnull    | Arp\DoctrineQueryFilter\Filter\IsNull | Test if a is NULL | `field` |
| isnotnull    | Arp\DoctrineQueryFilter\Filter\IsNotNull | Test if a is NOT NULL | `field` |
| islike    | Arp\DoctrineQueryFilter\Filter\IsLike | Test if a is LIKE b | `field`, `value` |
| isnotlike    | Arp\DoctrineQueryFilter\Filter\IsNotLike | Check if a is NOT LIKE b | `field`, `value` |
| isin    | Arp\DoctrineQueryFilter\Filter\IsIn | Check if a is IN b | `field`, `value` |
| isnotin    | Arp\DoctrineQueryFilter\Filter\IsNotIn | Check if a is NOT IN b | `field`, `value` |

### Composing Filters

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

When defining more than one filter, conditions will be explicitly `AND` together using the `AndX` composite query filter. 
To instead create an `OR` condition, we must define a `orx` filter and provide it with the required `conditions` array.

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
    
## Filtering Examples

@todo More filtering examples

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
