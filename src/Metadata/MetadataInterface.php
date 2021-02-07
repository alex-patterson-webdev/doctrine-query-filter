<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

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
    public function getAssociationFiledMapping(string $fieldName): array;
}
