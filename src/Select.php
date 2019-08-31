<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Select as DoctrineSelect;

/**
 * Select
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Select implements QueryExpressionInterface
{
    /**
     * $spec
     *
     * @var mixed $spec
     */
    protected $spec;

    /**
     * __construct
     *
     * @param mixed  $spec
     */
    public function __construct($spec)
    {
        $this->spec = $spec;
    }

    /**
     * build
     *
     * Build the Select criteria section of the query.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder) : string
    {
        return (string) (new DoctrineSelect($this->spec));
    }

}