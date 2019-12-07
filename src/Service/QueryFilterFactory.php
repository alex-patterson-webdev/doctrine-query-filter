<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\AndX;
use Arp\DoctrineQueryFilter\Equal;
use Arp\DoctrineQueryFilter\FieldName;
use Arp\DoctrineQueryFilter\GreaterThan;
use Arp\DoctrineQueryFilter\GreaterThanOrEqual;
use Arp\DoctrineQueryFilter\In;
use Arp\DoctrineQueryFilter\IsNotNull;
use Arp\DoctrineQueryFilter\IsNull;
use Arp\DoctrineQueryFilter\LessThan;
use Arp\DoctrineQueryFilter\LessThanOrEqual;
use Arp\DoctrineQueryFilter\NotEqual;
use Arp\DoctrineQueryFilter\OrX;
use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryExpressionFactoryException;

/**
 * QueryFilterFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class QueryFilterFactory implements QueryFilterFactoryInterface
{
    /**
     * queryFilterManager
     *
     * @var QueryFilterManager
     */
    protected $expressionManager;

    /**
     * __construct
     *
     * @param QueryFilterManager $expressionManager
     */
    public function __construct(QueryFilterManager $expressionManager)
    {
        $this->expressionManager = $expressionManager;
    }

    /**
     * andX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return AndX
     *
     * @throws QueryExpressionFactoryException
     */
    public function andX(...$spec) : AndX
    {
        /** @var AndX $expression */
        $expression = $this->create(AndX::class, $spec);

        return $expression;
    }

    /**
     * orX
     *
     * @param QueryFilterInterface[] ...$spec
     *
     * @return OrX
     *
     * @throws QueryExpressionFactoryException
     */
    public function orX(...$spec) : OrX
    {
        /** @var OrX $expression */
        $expression = $this->create(OrX::class, $spec);

        return $expression;
    }

    /**
     * eq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return Equal
     *
     * @throws QueryExpressionFactoryException
     */
    public function eq($a, $b) : Equal
    {
        /** @var Equal $expression */
        $expression = $this->create(Equal::class, func_get_args());

        return $expression;
    }

    /**
     * neq
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return NotEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function neq($a, $b) : NotEqual
    {
        /** @var NotEqual $expression */
        $expression = $this->create(NotEqual::class, func_get_args());

        return $expression;
    }

    /**
     * isNull
     *
     * @param string $fieldName
     *
     * @return IsNull
     *
     * @throws QueryExpressionFactoryException
     */
    public function isNull(string $fieldName) : IsNull
    {
        /** @var IsNull $expression */
        $expression = $this->create(IsNull::class, func_get_args());

        return $expression;
    }

    /**
     * isNotNull
     *
     * @param string $fieldName
     *
     * @return IsNotNull
     *
     * @throws QueryExpressionFactoryException
     */
    public function isNotNull(string $fieldName) : IsNotNull
    {
        /** @var IsNotNull $expression */
        $expression = $this->create(IsNotNull::class, func_get_args());

        return $expression;
    }

    /**
     * lt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThan
     *
     * @throws QueryExpressionFactoryException
     */
    public function lt($a, $b) : LessThan
    {
        /** @var LessThan $expression */
        $expression = $this->create(LessThan::class, func_get_args());

        return $expression;
    }

    /**
     * lte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return LessThanOrEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function lte($a, $b) : LessThanOrEqual
    {
        /** @var LessThanOrEqual $expression */
        $expression = $this->create(LessThanOrEqual::class, func_get_args());

        return $expression;
    }

    /**
     * gt
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThan
     *
     * @throws QueryExpressionFactoryException
     */
    public function gt($a, $b) : GreaterThan
    {
        /** @var GreaterThan $expression */
        $expression = $this->create(GreaterThan::class, func_get_args());

        return $expression;
    }

    /**
     * gte
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return GreaterThanOrEqual
     *
     * @throws QueryExpressionFactoryException
     */
    public function gte($a, $b) : GreaterThanOrEqual
    {
        /** @var GreaterThanOrEqual $expression */
        $expression = $this->create(GreaterThanOrEqual::class, func_get_args());

        return $expression;
    }

    /**
     * in
     *
     * @param string $fieldName
     * @param array  $collection
     *
     * @return In
     *
     * @throws QueryExpressionFactoryException
     */
    public function in(string $fieldName, $collection) : In
    {
        /** @var In $expression */
        $expression = $this->create(In::class, func_get_args());

        return $expression;
    }

    /**
     * getFieldName
     *
     * Return a field name string with the desired alias prepended.
     *
     * @param string      $fieldName
     * @param string|null $alias
     *
     * @return FieldName
     *
     * @throws QueryExpressionFactoryException
     */
    public function fieldName(string $fieldName, string $alias = null) : FieldName
    {
        /** @var FieldName $expression */
        $expression = $this->create(FieldName::class, func_get_args());

        return $expression;
    }

    /**
     * create
     *
     * Create a new filter and seed it with the provided arguments.
     *
     * @param mixed  $spec    The name of the query filter to create.
     * @param array  $args    The query filter's arguments.
     * @param array  $options The optional factory options.
     *
     * @return QueryFilterInterface
     *
     * @throws QueryExpressionFactoryException
     */
    public function create($spec, array $args = [], array $options = []) : QueryFilterInterface
    {
        $expression = null;

        if (is_array($spec)) {
            $specs = (array_values($spec) === $spec) ? $spec : [$spec];

            $expressions = [];

            foreach ($specs as $index => $spec) {

                if ($spec instanceof QueryFilterInterface) {
                    $expressions[] = $spec;
                    continue;
                }
                elseif (! is_array($spec)) {
                    continue;
                }

                if (! isset($spec['filter'])) {

                    throw new QueryExpressionFactoryException(sprintf(
                        'Error for array index \'%d\'; query filter specification must define a \'filter\'.',
                        $index
                    ));
                }
                elseif (! is_string($spec['filter'])) {

                    throw new QueryExpressionFactoryException(sprintf(
                        'Error for array index \'%d\'; query filter specification \'filter\' must be a string.',
                        $index
                    ));
                }

                $filter = $spec['filter'];
                unset($spec['filter']);

                $expressions[] = $this->create($filter, $spec, $options);
            }

            $expression = $this->andX(...$expressions);
        }
        elseif (is_string($spec)) {

            if (! $this->expressionManager->has($spec)) {

                throw new QueryExpressionFactoryException(sprintf(
                    'Failed to find a valid query filter matching \'%s\'.',
                    $spec
                ));
            }

            $config = [
                'config' => [
                    'arguments' => $args,
                    'options'   => $options
                ],
            ];

            try {
                $expression = $this->expressionManager->build($spec, $config);
            }
            catch(\Exception $e) {

                throw new QueryExpressionFactoryException(
                    sprintf(
                        'Unable to create new query filter \'%s\' : %s',
                        $spec,
                        $e->getMessage()
                    ),
                    $e->getCode(),
                    $e
                );
            }
        }
        elseif ($spec instanceof QueryFilterInterface) {
            $expression = $spec;
        }

        if (! $expression instanceof QueryFilterInterface) {

            throw new QueryExpressionFactoryException(
                'The query expression factory was unable to resolve the provided specification.'
            );
        }

        return $expression;
    }

}