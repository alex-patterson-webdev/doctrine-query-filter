<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\Service\Exception\QueryException;
use Doctrine\ORM\AbstractQuery as DoctrineAbstractQuery;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * Query
 *
 * Wraps the Doctrine AbstractQuery in the QueryInterface.
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
class Query implements QueryInterface
{
    /**
     * $query
     *
     * @var DoctrineAbstractQuery
     */
    protected $query;

    /**
     * __construct.
     *
     * @param DoctrineAbstractQuery $query
     * @param array                 $options
     *
     * @throws QueryException
     */
    public function __construct(DoctrineAbstractQuery $query, array $options = [])
    {
        $this->query = $query;

        if (! empty($options)) {
            $this->configure($options);
        }
    }

    /**
     * getSQL
     *
     * Return the query SQL representation.
     *
     * @return string
     *
     * @throws QueryException
     */
    public function getSQL() : string
    {
        try {
            return $this->getDoctrineQuery()->getSQL();
        }
        catch (\Exception $e) {

            throw new QueryException(
                sprintf('Failed to generate query SQL string : %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * execute
     *
     * @param array $options
     *
     * @return mixed
     *
     * @throws QueryException
     */
    public function execute(array $options = [])
    {
        try {
            return $this->getDoctrineQuery($options)->execute();
        }
        catch (\Exception $e) {

            throw new QueryException(
                sprintf(
                    'Failed to execute query : %s',
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * getDoctrineQuery
     *
     * Return the DoctrineQuery instance.
     *
     * @param array $options
     *
     * @return DoctrineAbstractQuery
     *
     * @throws QueryException  If the instance cannot be returned.
     */
    public function getDoctrineQuery(array $options = [])
    {
        if (! empty($options)) {
            $this->configure($options);
        }

        return $this->query;
    }

    /**
     * configure
     *
     * Configure the Doctrine query.
     *
     * @param array $options  The query options.
     *
     * @return QueryInterface
     *
     * @throws QueryException
     */
    public function configure(array $options): QueryInterface
    {
        $query = $this->query;

        if (! empty($options)) {

            if ($query instanceof DoctrineQuery) {

                foreach ($options as $name => $value) {

                    try {
                        switch ($name) {

                            case 'dql' :
                                $query->setDQL($value);
                            break;

                            case 'lock_mode' :
                                $query->setLockMode($value);
                            break;

                            case 'result_set_mapping' :
                                $query->setResultSetMapping($value);
                            break;
                        }
                    }
                    catch (\Exception $e) {

                        throw new QueryException(
                            sprintf(
                                'Unable to set query option \'%s\' : %s',
                                $name,
                                $e->getMessage()
                            ),
                            $e->getCode(),
                            $e
                        );
                    }
                }
            }

            foreach ($options as $name => $value) {

                try {
                    switch ($name) {

                        case 'hydration_mode' :
                            $query->setHydrationMode($value);
                        break;

                        case 'hints' :
                            if (is_array($value)) {
                                foreach($value as $hintName => $hintValue) {
                                    $query->setHint($hintName, $hintValue);
                                }
                            }
                        break;

                        case 'cache_mode' :
                            $query->setCacheMode($value);
                        break;

                        case 'cache_lifetime' :
                            $query->setLifetime($value);
                        break;

                        case 'cache_region' :
                            $query->setCacheRegion($value);
                        break;
                    }
                }
                catch (\Exception $e) {

                    throw new QueryException(
                        sprintf(
                            'Unable to set query option \'%s\' : %s',
                            $name,
                            $e->getMessage()
                        ),
                        $e->getCode(),
                        $e
                    );
                }

            }
        }

        return $this;
    }

}