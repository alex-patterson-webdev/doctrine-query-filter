<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\From as DoctrineFrom;

/**
 * From
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class From implements QueryExpressionInterface
{
    /**
     * $spec
     *
     * @var string
     */
    protected $spec;

    /**
     * $alias
     *
     * @var string
     */
    protected $alias;

    /**
     * $indexBy
     *
     * @var null|string
     */
    protected $indexBy;

    /**
     * __construct.
     *
     * @param string      $spec
     * @param string      $alias
     * @param null|string $indexBy
     */
    public function __construct(string $spec, string $alias, $indexBy = null)
    {
        $this->spec    = $spec;
        $this->alias   = $alias;
        $this->indexBy = $indexBy;
    }

    /**
     * build
     *
     * Construct a DQL 'expression' string.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder): string
    {
        return (string) (new DoctrineFrom($this->spec, $this->alias, $this->indexBy));
    }

}