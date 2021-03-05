<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Metadata
 */
interface MetadataInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField(string $fieldName): bool;

    /**
     * @param string $fieldName
     *
     * @return array
     *
     * @throws MetadataException
     */
    public function getFieldMapping(string $fieldName): array;

    /**
     * @param string $fieldName
     *
     * @return string
     *
     * @throws MetadataException
     */
    public function getFieldType(string $fieldName): string;

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasAssociation(string $fieldName): bool;

    /**
     * @param string $fieldName
     *
     * @return array
     *
     * @throws MappingException
     */
    public function getAssociationMapping(string $fieldName): array;
}
