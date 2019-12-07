<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * FieldName
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class FieldName implements QueryFilterInterface
{
    /**
     * $fieldName
     *
     * @var string
     */
    protected $fieldName;

    /**
     * $alias
     *
     * @var string
     */
    protected $alias;

    /**
     * __construct.
     *
     * @param string $fieldName
     * @param string $alias
     */
    public function __construct(string $fieldName, string $alias = '')
    {
        $this->fieldName = $fieldName;
        $this->alias     = $alias;
    }

    /**
     * build
     *
     * Construct a DQL 'expression' string.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @param array                 $criteria
     *
     * @return string
     */
    public function filter(QueryBuilderInterface $queryBuilder, array $criteria)
    {
        if (empty($this->alias) || false !== strpos($this->fieldName, '.')) {
            return $this->fieldName;
        }

        return $this->alias . '.' . $this->fieldName;
    }

}