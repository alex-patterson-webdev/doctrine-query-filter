# Arp\DoctrineQueryFilter

## About

This module provides a wrapper around the [Doctrine ORM](https://github.com/doctrine/orm) `QueryBuilder` that allows developers to create object based DQL query components which aims to promote flexibility, reusability and testability.

## Why?

The [Doctrine ORM](https://github.com/doctrine/orm) `QueryBuilder` is an already powerful *method* based abstraction of constructing `DQL`.

Building queries using the `QueryBuilder` however can quickly become repetitive and inflexible in larger applications. The implementation often results in 
custom `EntityRepository` classes defining a long list of similar `findByFoo()` or `findOneByBar()` methods. These methods can take complex optional
 arguments in order to filter for a collection of entities. When client requirements change, you might find yourself coping boilerplate query building methods for very minor changes or
 manually constructing sections of DQL repetitively. Such a "copy-pasting" approach can bloat code and cause additional head aches when queries need to be modified.
 
The `Arp\DoctrineQueryFilter` module solves these issues by allowing developers to create a simple object that  encapsulates sections of `DQL`.
 
## Installation

 Installation via [Composer](https://getcomposer.org)

    composer require alex-patterson-webdev/doctrine-query-filter ^1
 
## The QueryBuilder

In order to start constructing and executing queries, we must create a `Arp\DoctrineQueryFilter\Service\QueryBuilder` instance and
provide it with both a `Arp\DoctrineQueryFilter\Service\QueryFilterFactory` and a Doctrine `Doctrine\ORM\QueryBuilder` instance.
 
    use \Arp\DoctrineQueryFilter\Service\QueryBuilder;
    use \Arp\DoctrineQueryFilter\Service\QueryFilterFactory;
    use \Arp\DoctrineQueryFilter\Service\QueryFilterManager;
   
    $config = [];
   
    // Dependency injection container for QueryFilter instances.
    $container = new QueryFilterManager($config);
   
    // Factory to create query filters, can be reused in other query builders
    $queryFilterFactory = new QueryFilterFactory($container);
    
    // Assuming $entityManager is a Doctirne\ORM\EntityManager.
    $queryBuilder = new QueryBuilder(
        $entityManager->createQueryBuilder(), 
        $queryFilterFactory 
    );
 
The new `$queryBuilder` instance has a *similar* API to the internal `Doctrine` implementation most Doctrine users are familiar with. For instance, a simple DQL
query can be created as below.

    $queryBuilder->select('p.id, p.name')
                 ->from('Products', 'p')
                 ->where('p.deleted = 0 AND p.productName = :name');
    
    // Arp\DoctrineQueryFilter\Service\QueryInterface
    $query = $queryBuilder->getQuery(); 
   
    $results = $query->execute(['productName' => 'Test']);
    
Although possible, this simple query construction offers no real benefit over the Doctrine Query Builder. The true power of 
this module lies with the ability to compose many `QueryFilterInterface` instances, encapsulating them as self contained query expressions.
    
## What are Query Filters?    
           
Query Filters are classes that implement `Arp\DoctrineQueryFilter\Service\QueryFilterInterface`. We use Query Filters to create DQL expressions. 
These 'expressions' and simply strings that can include just small parts of a bigger query that can be added together. 

The interface requires the implementation of a single method, `build(QueryBuilderInterface $queryBuilder) : string`.
 
## A Simple Example 
    
Consider if we re-wrote out earlier DQL query using QueryFilter instances as reusable expressions. 
The brackets `[]` indicate our desired QueryFilter objects and how we intent to compose them. 

    [SELECT p.id, p.name FROM \Foo\Entity\Products as p WHERE [[p.deleted = 0] AND [p.productName = :productName]]]

We create a new query filter, implementing `QueryFilterInterface::build()` for the `p.productName = :productName` filtering.

    class ProductName implements QueryFilterInterface
    {
        protected $fieldName = 'productName';
    
        public function __construct(string $name, string $fieldName = '')
        {
            $this->name = $name;
        
            if (! empty($fieldName)) {
                $this->fieldName = $fieldName;
            }
        }
    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            $queryBuilder->andWhere(
                $queryBuilder->factory()->eq($this->fieldName, $this->name)
            );
        }
    }
    
And another query filter for the 'not deleted' where criteria. It might be more useful to negate a "IsDeleted" query filter

    class IsDeleted implements QueryFilterInterface
    {
        protected $fieldName;
        
        protected $deleted;
    
        public function __construct(bool $deleted = true, string $fieldName = 'deleted')
        {
            $this->fieldName = $fieldName;
            $this->deleted   = $deleted;
        }
    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            $queryBuilder->andWhere(
                $queryBuilder->factory()->eq($this->fieldName, $this->deleted)
            );
        }
    }
    
We can then combine these filters together within another Query Filter using logical operators `$factory->andX()`. The `andX()` and related `orX()`.

    class ProductNameSearch implements QueryFilterInterface
    {    
        public function build(QueryBuilderInterface $queryBuilder) : string
        {
            // Instance of Arp\DoctrineQueryFilter\Service\
            $factory = $queryBuilder->factory();
    
            $queryBuilder->andWhere(
                $factory->andX(
                    $factory->create(ProductName::class, ['Hot Dog']),
                    $factory->create(IsDeleted::class, [false])
                )
            );
        }
    }  
    
The example shows that we can access our custom filters form the `QueryFilterFactory` via `QueryBuilderFactory::create($queryFilterName, $arguments)`.

This factory internally uses a Zend Framework 3 [PluginManager](https://docs.zendframework.com/zend-servicemanager/plugin-managers/); so we can create
 query filters with factory classes and use the container to inject dependencies.
 
By default all `$filterName` arguments that map **directly** to fully qualified class names of the filter will be created
by the `QueryFilterFactory`; if 

    use \Arp\DoctrineQueryFilter\Factory\Service\QueryFilterFactory;
    use \Arp\DoctrineQueryFilter\Service\QueryFilterManager;
   
    $config = [
        'factories' => [
            ProductNameSearch::class => QueryFilterFactory::class,
            ProductName::class       => QueryFilterFactory::class,
            IsDeleted::class         => QueryFilterFactory::class,
        ]
    ];
    
    $container = new QueryFilterManager($config);
    
We can now reference just the `ProductNameSearch` filter where we need to query.

    $queryBuilder->andWhere(
        $factory->create(ProductNameSearch::class)
    );

## QueryBuilderFactory

The `QueryBuilderFactory` has a number of other convenience methods to return Query Filters included by this module.

#### \Arp\DoctrineQueryFilter\Equal

`$factory->eq(1, 1)` will produce DQL string `1 = 1`.

#### \Arp\DoctrineQueryFilter\NotEqual

`$factory->neq(1, 1)` will produce DQL string `1 <> 1`.

#### \Arp\DoctrineQueryFilter\GreaterThan
 
`$factory->gt(2, 1)` will produce DQL string `2 > 1`.

#### \Arp\DoctrineQueryFilter\LessThan

`$factory->ln(1, 2)` will produce DQL string `1 < 2`.

#### \Arp\DoctrineQueryFilter\IsNull

`$factory->isNull('a.foo')` will produce DQL string `1 IS NULL`.

#### \Arp\DoctrineQueryFilter\AndX 

`$factory->andX($filter1, $filter2, $filter3)` will produce DQL string `f1 AND f2 AND f3`.

#### \Arp\DoctrineQueryFilter\Orx 

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