<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr;

/**
 * AndX
 *
 * Combine two or more expressions with and AND expression.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class AndX extends AbstractComposite
{
    /**
     * build
     *
     * Apply filtering to the provided query builder.
     *
     * @param QueryBuilderInterface $queryBuilder  The query filter
     * @param array                 $criteria      The filtering criteria.
     *
     * @return void
     *
     * @throws QueryFilterException  If the query filtering cannot be applied
     */
    public function filter(QueryBuilderInterface $queryBuilder, array $criteria)
    {
        $conditions = empty($criteria['conditions']) ? $criteria['conditions'] : [];

        if (! is_array($conditions)) {

            throw new QueryFilterException(sprintf(
                'The \'$conditions\' argument must be of type \'array\'; \'%s\' provided in \'%s\'.',
                gettype($conditions),
                __METHOD__
            ));
        }

        $factory = $queryBuilder->getFilterFactory();

        $andX = (new Expr())->andX();

        foreach ($conditions as $filter) {
            $queryFilter = $queryBuilder->getFilterFactory()->create($filter);

            if ($queryFilter) {
                $queryFilter->filter($queryBuilder);
            }
        }


        if (empty($this->queryFilters)) {
            return '';
        }


        foreach($this->queryFilters as $queryFilter) {
            $andX->add($queryFilter->filter($queryBuilder, $criteria));
        }

        return (string) $andX;
    }


}