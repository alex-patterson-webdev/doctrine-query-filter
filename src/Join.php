<?php

namespace Arp\DoctrineQueryFilter;

use Arp\DoctrineQueryFilter\Service\QueryBuilderInterface;
use Doctrine\ORM\Query\Expr\Join as DoctrineJoin;

/**
 * Join
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
class Join implements QueryFilterInterface
{
    /**
     * @const
     */
    const JOIN_INNER = DoctrineJoin::INNER_JOIN;
    const JOIN_LEFT  = DoctrineJoin::LEFT_JOIN;

    const TYPE_WITH = DoctrineJoin::WITH;
    const TYPE_ON   = DoctrineJoin::ON;

    /**
     * $type
     *
     * @var string
     */
    protected $type = self::JOIN_INNER;

    /**
     * $join
     *
     * @var string
     */
    protected $join;

    /**
     * $alias
     *
     * @var string
     */
    protected $alias;

    /**
     * $conditions
     *
     * @var mixed
     */
    protected $conditions;

    /**
     * $conditionType
     *
     * @var string
     */
    protected $conditionType = self::TYPE_WITH;

    /**
     * $indexBy
     *
     * @var null|string
     */
    protected $indexBy;

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
        try {

            $conditions = empty($this->conditions) ? null : $this->conditions;



            $join = new DoctrineJoin(
                $this->type,
                $this->join,
                $this->alias,
                $this->conditionType,
                $conditions,
                $this->indexBy
            );

            return (string) $join;
        }
        catch(\Exception $e) {

        }
    }

}