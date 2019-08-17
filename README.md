# Arp\DoctrineQueryFilter

Say goodbye to repository method hell and and hello to the `Arp\DoctrineQueryFilter` module.

This module is designed to allow Doctrine DQL to be constructed via composing multiple query objects together. 
The services provide a wrapper around the existing QueryBuilder that very easily allows extention to create object based
queries.

For example, a simple Doctrine query can now be user 

    public function getDeletedUsers()
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $expr = $queryBuilder->expr();
        
        $queryBuilder->where($expr->eq('a.deleted', ':deleted'));       
    
        $query = $queryBuilder->getQuery();
        
        return $query->execute();
    }

Would become

 