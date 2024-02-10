![build](https://github.com/alex-patterson-webdev/doctrine-query-filter/actions/workflows/workflow.yml/badge.svg)
[![codecov](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter/branch/master/graph/badge.svg)](https://codecov.io/gh/alex-patterson-webdev/doctrine-query-filter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alex-patterson-webdev/doctrine-query-filter/?branch=master)

# Doctrine Query Filter

A package providing query filtering components for Doctrine ORM. By modeling query filter criteria as reusable objects, 
it offers a consistent and extendable way of constructing complex DQL statements.

The project has been inspired by the [Laminas Doctrine QueryBuilder](https://github.com/laminas-api-tools/api-tools-doctrine-querybuilder); 
providing similar functionality without the Laminas Framework dependency. 

## Installation

Installation via [composer](https://getcomposer.org).

    require alex-patterson-webdev/doctrine-query-filter ^0.9

## Usage

Using the `QueryFilterManager` we can create DQL strings from an `array` format. For example, consider the following DQL string.

    SELECT c FROM Customer c WHERE c.forename = 'Fred' AND (c.age BETWEEN 18 AND 30)

We can represent this DQL query using a collection of _filters_, known as our query _criteria_

    $criteria = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'forename',
                'value' => 'Fred',
            ],
            [
                'name' => 'between',
                'field' => 'age',
                'from' => 18,
                'to' => 30
            ],
        ],
    ];

By passing this `$criteria` to our `QueryFilterManager` we can generate (and execute) the query in the following way.

    // Get our Doctrine query builder instance
    $queryBuilder = $entityManager->getRepository('Customer')->createQueryBuilder('c');

    // Create a new QueryFilterManager (and supply it with a desired FilterFactory instance)
    $queryFilterManager = new QueryFilterManager(new FilterFactory());

    // Apply the filters to the $queryBuilder
    $queryBuilder = $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

    // SELECT c FROM Customer c WHERE c.forename = 'Fred' AND (c.age BETWEEN 18 AND 30)
    echo $queryBuilder->getDQL();

    // Fetch the results
    $results = $queryBuilder->getQuery()->execute();

### Combining filters with an OR condition

When defining more than one filter, conditions will be explicitly `AND` together using the `AndX` composite query filter.
To instead create an `OR` condition, we must define a `orx` filter and provide it with the required `conditions` array.

    // SELECT c FROM Customer c WHERE c.enabled = :enabled AND (c.username = :username1 OR c.username = :username2)
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

### Nesting Filters

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

## Custom Filters

The above examples demonstrate the use of the built-in filters. However, these are very verbose and can be difficult to manage.
The true power of the `QueryFilterManager` is the ability to create and use custom filters; by extending the `AbstractFilter` class. 
Custom filters are self-contained and reusable across multiple queries. This allows for a more modular and maintainable approach to build complex queries.

The below example demonstrates how we could utilise the provided filters to create our own `Customer` that accepts optional `$criteria` parameters.

    use Arp\DoctrineQueryFilter\Filter\AbstractFilter;
    use Arp\DoctrineQueryFilter\Filter\Exception\FilterException;
    use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
    use Arp\DoctrineQueryFilter\QueryBuilderInterface;

    final class Customer extends AbstractFilter
    {
        public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
        {
            if (empty($criteria['surname'])) {
                throw new FilterException('The surname criteria is required');
            }

            $filters = [
                [
                    'name' => 'neq',
                    'field' => 'status',
                    'value' => 'inactive',
                ],
                [
                    'name' => 'begins_with',
                    'field' => 'surname',
                    'value' => $criteria['surname'],
                ],
            ];

            if (isset($criteria['forename'])) {
                $filters[] = [
                    'name' => 'eq',
                    'field' => 'forename',
                    'value' => $criteria['forename'],
                ];
            }

            if (isset($criteria['age'])) {
                $filters[] = [
                    'name' => 'gte',
                    'field' => 'age',
                    'value' => $criteria['age'],
                ];
            }

            $this->applyFilters($queryBuilder, $metadata, $filters);
        }
    }

    // We must register the custom filter with the FilterFactory
    $filterFactory = new FilterFactory();
    $filterFactory->addFilter('my_customer_filter', CustomFilter::class);

    $queryFilterManager = new QueryFilterManager($filterFactory);
    $criteria = [
        'filters' => [
            [
                'name' => 'my_customer_filter',
                'surname' => 'Smith',
                'age' => 21,
            ],
        ],
    ];

    // SELECT c FROM Customer c WHERE c.status != 'inactive' AND c.surname LIKE 'Smith%' AND c.age >= 21
    $queryBuilder = $queryFilterManager->filter($queryBuilder, 'Customer', $criteria);

## Sorting Results

In addition to filtering collections, we can also add sorting by using the `sort` criteria key to add Sort Fillers.

    // SELECT c FROM Customer c WHERE c.id = 123 ORDER BY c.id DESC, c.createdDate ASC
    $critiera = [
        'filters' => [
            [
                'name' => 'eq',
                'field' => 'id',
                'value' => 123
            ],
            'sort' => [
                [
                    'name' => Field::class, 
                    'field' => 'id',
                    'direction' => OrderByDirection::DESC->value
                ],
                [
                    'field' => 'createdDate'
                ],
            ]
        ]
    ];

Each sort filter requires the `field` key, with an optional `direction` of `ASC` or `DESC`.
Omitting the `name` key from a sort filter will apply a  `Arp\DoctrineQueryFilter\Sort\Field` sort filter by default. In addition,
omitting the `direction` will by default make the sort direction `ASC`.

## Filter Reference

There are many types of query filters already included. The table below defines the filter aliases and their available options.

| Alias       |                     Class Name                      |                     Description                      |   Required Options    |                  Optional Options                   |
|-------------|:---------------------------------------------------:|:----------------------------------------------------:|:---------------------:|:---------------------------------------------------:|
| eq          |       Arp\DoctrineQueryFilter\Filter\IsEqual        |              Test is `field` = `value`               |   `field`, `value`    |                  `alias`, `format`                  |
| neq         |      Arp\DoctrineQueryFilter\Filter\IsNotEqual      |              Test is `field` != `value`              |   `field`, `value`    |                  `alias`, `format`                  |
| gt          |    Arp\DoctrineQueryFilter\Filter\IsGreaterThan     |              Test is `field` > `value`               |   `field`, `value`    |                  `alias`, `format`                  |
| gte         | Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual |              Test is `field` >= `value`              |   `field`, `value`    |                  `alias`, `format`                  |
| lt          |      Arp\DoctrineQueryFilter\Filter\IsLessThan      |              Test is `field` < `value`               |   `field`, `value`    |                  `alias`, `format`                  |
| lte         |  Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual   |              Test is `field` <= `value`              |   `field`, `value`    |                  `alias`, `format`                  |
| and         |         Arp\DoctrineQueryFilter\Filter\AndX         |    Join two or more expressions using logical AND    |     `conditions`      |                                                     |
| or          |         Arp\DoctrineQueryFilter\Filter\OrX          |    Join two or more expressions using logical OR     |     `conditions`      |                                                     |
| between     |      Arp\DoctrineQueryFilter\Filter\IsBetween       |    Test if `field` => `from` and `field` <= `to`     | `field`, `from`, `to` |                  `alias`, `format`                  |
| member_of   |      Arp\DoctrineQueryFilter\Filter\IsMemberOf      |  Test if  `value` exists within collection `field`   |   `field`, `value`    |                  `alias`, `format`                  |
| is_null     |        Arp\DoctrineQueryFilter\Filter\IsNull        |               Test if `field` is NULL                |        `field`        |                  `alias`, `format`                  |
| not_null    |      Arp\DoctrineQueryFilter\Filter\IsNotNull       |             Test if `field` is NOT NULL              |        `field`        |                  `alias`, `format`                  |
| like        |        Arp\DoctrineQueryFilter\Filter\IsLike        |           Test if `field` is LIKE `value`            |   `field`, `value`    |                  `alias`, `format`                  |
| not_like    |      Arp\DoctrineQueryFilter\Filter\IsNotLike       |         Check if `field` is NOT LIKE `field`         |   `field`, `value`    |                  `alias`, `format`                  |
| in          |         Arp\DoctrineQueryFilter\Filter\IsIn         |            Check if `field` is IN `field`            |   `field`, `value`    |                  `alias`, `format`                  |
| not_in      |       Arp\DoctrineQueryFilter\Filter\IsNotIn        |          Check if `field` is NOT IN `value`          |   `field`, `value`    |                  `alias`, `format`                  |
| begins_with |      Arp\DoctrineQueryFilter\Filter\BeginsWith      |         Check if `field` beings with `value`         |   `field`, `value`    |                  `alias`, `format`                  |
| ends_with   |       Arp\DoctrineQueryFilter\Filter\EndsWith       |          Check if `field` ends with `value`          |   `field`, `value`    |                  `alias`, `format`                  |
| empty       |       Arp\DoctrineQueryFilter\Filter\IsEmpty        |      Check if `field` is equal to ('' or NULL)       |        `field`        |                                                     |
| left_join   |       Arp\DoctrineQueryFilter\Filter\LeftJoin       | Apply left join to `field` with optional conditions  |        `field`        | `alias`, `conditions`, `condition_type`, `index_by` |
| inner_join  |      Arp\DoctrineQueryFilter\Filter\InnerJoin       | Apply inner join to `field` with optional conditions |        `field`        | `alias`, `conditions`, `condition_type`, `index_by` |

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

## Unit tests

Unit tests can be executed using PHPUnit from the application root directory.

    php vendor/bin/phpunit
