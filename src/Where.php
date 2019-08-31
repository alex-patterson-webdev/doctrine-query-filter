<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\Exception\QueryExpressionFactoryException;
use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * Where
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Where implements QueryExpressionInterface
{
    /**
     * $spec
     *
     * @var mixed
     */
    protected $spec;

    /**
     * __construct
     *
     * @param mixed $spec
     */
    public function __construct(...$spec)
    {
        $this->spec = $spec;
    }

    /**
     * build
     *
     * Construct a DQL 'expression' string.
     *
     * @param QueryBuilderInterface $queryBuilder
     *
     * @return string
     *
     * @throws QueryExpressionFactoryException
     */
    public function build(QueryBuilderInterface $queryBuilder) : string
    {
        if (count($this->spec) !== 1 || ! $this->spec instanceof AbstractComposite) {
            $this->spec = $queryBuilder->expr()->andX(...$this->spec);
        }

        return $this->spec->build($queryBuilder);
    }

}