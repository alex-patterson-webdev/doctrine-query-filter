<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\Exception\InvalidOptionException;
use Arp\DoctrineQueryFilter\Service\Exception\QueryBuilderException;
use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterException;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * Equal
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Equal extends AbstractQueryFilter
{
    /**
     * build
     *
     * Build the query filter expression.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @throws QueryFilterException
     */
    public function filter(QueryBuilderInterface $queryBuilder)
    {
        $fieldName = $this->getOption('field_name');

        if (empty($fieldName)) {
            throw InvalidOptionException::missingOptionException('field_name', static::class);
        }

        $value = $this->getOption('value');

        if (! isset($value)) {
            throw InvalidOptionException::missingOptionException('value', static::class);
        }

        $where  = ($this->getOption('where', 'and') === 'and' ? 'and' : 'or');
        $alias  = $this->getOption('alias', $queryBuilder->getAlias());
        $format = $this->getOption('format');

        $parameter = uniqid($alias);
        $spec      = $queryBuilder->expr()->eq($alias . '.' . $fieldName, ':' . $parameter);

        $queryBuilder->setParameter($parameter, $value, $format);

        try {
            if ('or' === $where) {
                $queryBuilder->orWhere($spec);
            }
            else {
                $queryBuilder->andWhere($spec);
            }
        }
        catch (QueryBuilderException $e) {

            throw new QueryFilterException(
                sprintf(
                    'Failed to apply \'%s\' condition in query filter \'%s\' : %s',
                    $where,
                    static::class,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

}