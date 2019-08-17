# Arp\DoctrineQueryFilter

## About

This module provides a wrapper around the [Doctrine ORM](https://github.com/doctrine/orm) `QueryBuilder` that allows developers to create object based query filters 
that promote flexibility and reusability.

## Why?

The [Doctrine ORM](https://github.com/doctrine/orm) `QueryBuilder` is an already powerful *method* based abstraction of constructing `DQL` (which is itself an abstraction of SQL). 

Building queries using the `QueryBuilder` however can quickly become repetitive and inflexible in larger applications. The implementation often results in 
custom `EntityRepository` classes defining a long list of similar `findByFoo()` or `findOneByBar()` methods. These methods can take complex optional
 arguments in order to filter for a collection of entities. When client requirements change, you might find yourself coping boilerplate query building methods for very minor changes or
 manually constructing sections of DQL repetitively. 
 
## How? 
 
 This module solves these issues by allowing developers to encapsulate sections of `DQL` as objects and compose these together to form complex queries.
 
## Installation

 Installation via [Composer](https://getcomposer.org)

    composer require alex-patterson-webdev/doctrine-query-filter ^1
 
## The QueryBuilder

In order to start constructing and executing queries, we must create a `Arp\DoctrineQueryFilter\Service\QueryBuilder` instance and
provide it both a `QueryFilterFactory` and a Doctrine `QueryBuilder` instance.
 
    use \Arp\DoctrineQueryFilter\Service\QueryBuilder;
    use \Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
    use \Arp\DoctrineQueryFilter\Service\QueryFilterManager;
   
    // Dependency injection container for QueryFilter instances.
    $container = new QueryFilterManager();
   
    // Factory to create query filters, can be reused in other query builders
    $queryFilterFactory = new QueryFilterFactory($container);

    $queryBuilder = new QueryBuilder(
        $entityManager->createQueryBuilder(), // Assuming $entityManager is a Doctirne\ORM\EntityManager.
        $queryFilterFactory 
    );

As our new query builder is simply a wrapper around the injected Doctrine instance, we can interact with 
this new `$queryBuilder` instance in a very similar way to the existing `Doctrine` implementation.

    $queryBuilder->select('p.id, p.name')
                 ->from('Products', 'p')
                 ->where('p.deleted = 0 AND p.name = :name');
    
    // Arp\DoctrineQueryFilter\Service\QueryInterface
    $query = $queryBuilder->getQuery(); 
   
    $results = $query->execute(['name' => 'Test']);
    
Although possible, this simple query construction offers no real benefit over the shipped Doctrine Query Builder. The true power of 
this module lies with the ability to compose many `QueryFilterInterface` instances, encapsulating them as self contained query filters.
    
## What are Query Filters?    
             
Query Filters are classes that implement `Arp\DoctrineQueryFilter\Service\QueryFilterInterface`. We use Query Filters to create DQL strings. 
These strings can include the entire DQL query or just small expressions of a bigger one. 

The interface requires the implementation of one method, `build(QueryBuilderInterface $queryBuilder) : string`. This method provides the
query builder that can then be modified by adding criteria.
 
## Example 
    
Consider if we re-wrote out earlier query using QueryFilter instances as reusable expressions; for example :

We create a new query filter, implementing `QueryFilterInterface::build()` for the 'product name' filtering.

    class ProductName implements QueryFilterInterface
    {
        protected $name;
        
        protected $alias;
    
        public function __construct(string $name, string $alias = null)
        {
            $this->name = $name;
            if ($alias) {
                $this->alias = $alias;
            }
        }
    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            $alias = isset($this->alias) ? $this->alias : $queryBuilder->getAlias();
    
            $factory = $queryBuilder->factory();
    
            $queryBuilder->andWhere($factory->eq($alias . 'name', $this->name));
        }
    }
    
And another query filter for the 'deleted' where criteria.

    class IsDeleted implements QueryFilterInterface
    {
        protected $deleted;
    
        protected $alias;
    
        public function __construct(bool $deleted, string $alias = null)
        {
            $this->deleted = $deleted;
            $this->alias   = $alias;
        }
    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            $alias = isset($this->alias) ? $this->alias : $queryBuilder->getAlias();
    
            $factory = $queryBuilder->factory();
    
            $queryBuilder->andWhere($factory->eq($alias . '.deleted', $this->deleted));
        }
    }
    
We now have filters that can be reused in any future queries! Simply compose them together using the pre-existing filters.

    class ProductNameSearch implements QueryFilterInterface
    {    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            $factory = $queryBuilder->factory();
    
            $queryBuilder->andWhere(
                $factory->andX(
                    $factory->create(ProductName::class),
                    $factory->create(IsDeleted::class, [false]) // We pass arguments to the construtor
                )
            );
        }
    }

In the above example we composed both the `ProductName` and `IsDeleted` Query Filters to create one, `ProductNameSearch`.
The example shows that we can access our custom filters via the `QueryBuilderFactory` via `$queryBuilder->factory()`.
    
Ensure that we now register our custom filters with the `QueryFilterManager` by passing the required configuration.

    use \Arp\DoctrineQueryFilter\Service\QueryFilterManager;
   
    $config = [
        'factories' => [
            ProductNameSearch::class => FilterFactory::class,
            ProductName::class       => FilterFactory::class,
            IsDeleted::class         => FilterFactory::class,
        ]
    ];
    
    $container = new QueryFilterManager(null, $config);
    
We can now reference just the compose filter `ProductNameSearch` where we need to query.

    $queryBuilder->andWhere(
        $factory->create(ProductNameSearch::class)
    );

## QueryBuilderFactory

The `QueryBuilderFactory` has a number of convenient methods to return Query Filters already provided by this module.

#### Arp\DoctrineQueryFilter\Equal

`$factory->eq(1, 1)` will produce DQL string `1 = 1`.

#### Arp\DoctrineQueryFilter\NotEqual

`$factory->neq(1, 1)` will produce DQL string `1 <> 1`.

#### `\Arp\DoctrineQueryFilter\GreaterThan`
 
`$factory->gt(2, 1)` will produce DQL string `2 > 1`.

#### `\Arp\DoctrineQueryFilter\LessThan` 

`$factory->ln(1, 2)` will produce DQL string `1 < 2`.

#### `\Arp\DoctrineQueryFilter\IsNull` 

`$factory->isNull('a.foo')` will produce DQL string `1 IS NULL`.

There are also composite query filters to join expressions together

#### `\Arp\DoctrineQueryFilter\AndX` 

`$factory->andX($filter1, $filter2, $filter3)` will produce DQL string `f1 AND f2 AND f3`.

#### `\Arp\DoctrineQueryFilter\Orx` 

`$factory->orX($filter1, $filter2, $filter3)` will produce DQL string `f1 OR f2 OR f3`.

## Unit Testing    
    
Unit testing via PHP unit.

    php vendor/bin/phpunit    
    
## Zend Framework 3
    
Although not required, this module integrates with Zend Framework 3. Simply add the module namespace to the module bootstrap array.

    $modules = [
        // ... bootstrap after any Doctrine Modules
        'Arp\\DoctrineQueryFilter',
        
        'Application',
    ];        
    
## Coming Soon

@todo Comprehensive documentation.