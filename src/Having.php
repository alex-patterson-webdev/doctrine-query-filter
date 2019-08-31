<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;

/**
 * Having
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Having implements QueryExpressionInterface
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
     * @throws Service\Exception\QueryExpressionFactoryException
     */
    public function build(QueryBuilderInterface $queryBuilder): string
    {
        if (count($this->spec) !== 1 || ! $this->spec instanceof AbstractComposite) {
            $this->spec = $queryBuilder->expr()->andX(...$this->spec);
        }

        return $this->spec->build($queryBuilder);
    }


}