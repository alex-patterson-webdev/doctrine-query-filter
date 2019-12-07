<?php

namespace Arp\DoctrineQueryFilter;

use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * MetadataAwareTrait
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter
 */
trait MetadataAwareTrait
{
    /**
     * $metadata
     *
     * @var ClassMetadata|null
     */
    protected $metadata;

    /**
     * hasMetadata
     *
     * @return bool
     */
    public function hasMetadata() : bool
    {
        return isset($this->metadata);
    }

    /**
     * getMetadata
     *
     * @return ClassMetadata|null
     */
    public function getMetadata() : ?ClassMetadata
    {
        return $this->metadata;
    }

    /**
     * setMetadata
     *
     * @param ClassMetadata|null $metadata
     */
    public function setMetadata(ClassMetadata $metadata = null)
    {
        $this->metadata = $metadata;
    }


}