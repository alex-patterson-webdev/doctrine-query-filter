<?php

namespace Arp\DoctrineQueryFilter\Service;

use Arp\DoctrineQueryFilter\QueryFilterInterface;
use Arp\DoctrineQueryFilter\Service\Exception\QueryFilterException;

/**
 * QueryFilterServiceInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\Doctrine\Service\Service
 */
interface QueryFilterServiceInterface
{
    /**
     * filter
     *
     * Perform a filtered search for a collection of entities.
     *
     * @param QueryFilterInterface $filter
     * @param array                $options
     *
     * @return object
     *
     * @throws QueryFilterException
     */
    public function filterOne($filter, array $options = []);

    /**
     * filter
     *
     * Perform a filtered search for a collection of entities.
     *
     * @param QueryFilterInterface|QueryFilterInterface[] $filter
     * @param array                                       $options
     *
     * @return object[]
     *
     * @throws QueryFilterException
     */
    public function filter($filter, array $options = []);

}