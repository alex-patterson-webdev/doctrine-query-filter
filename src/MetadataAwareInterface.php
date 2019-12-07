<?php

namespace Arp\DoctrineQueryFilter;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * MetadataAwareInterface
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
interface MetadataAwareInterface
{
    /**
     * hasMetadata
     *
     * @return bool
     */
    public function hasMetadata() : bool;

    /**
     * getMetadata
     *
     * @return ClassMetadata|null
     */
    public function getMetadata() : ?ClassMetadata;

    /**
     * setMetadata
     *
     * @param ClassMetadata|null $metadata
     */
    public function setMetadata(ClassMetadata $metadata = null);
}