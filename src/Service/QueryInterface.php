<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\Service\Exception\QueryException;
use Doctrine\ORM\AbstractQuery as DoctrineAbstractQuery;

/**
 * QueryInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service
 */
interface QueryInterface
{
    /**
     * execute
     *
     * @param array $options
     *
     * @return mixed
     *
     * @throws QueryException
     */
    public function execute(array $options = []);

    /**
     * getSQL
     *
     * @return string
     *
     * @throws QueryException
     */
    public function getSQL() : string;

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
    public function getDoctrineQuery(array $options = []) : DoctrineAbstractQuery;

    /**
     * configure
     *
     * Configure the query instance.
     *
     * @param array $options
     *
     * @return QueryInterface
     */
    public function configure(array $options) : QueryInterface;


}