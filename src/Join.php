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
class Join implements QueryExpressionInterface
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
     * @return string
     */
    public function build(QueryBuilderInterface $queryBuilder): string
    {
        try {
            $conditions = empty($this->conditions) ? null : $this->conditions;

            if (!empty($this->conditions) && !is_string($this->conditions)) {
                $conditions = $queryBuilder->expr()->create($this->conditions);
            }

            if ($this->conditions instanceof QueryExpressionInterface) {
                $conditions = $this->conditions->build($queryBuilder);
            }

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