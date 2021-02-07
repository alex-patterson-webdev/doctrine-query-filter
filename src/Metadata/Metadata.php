<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Metadata
 */
final class Metadata implements MetadataInterface
{
    /**
     * @var ClassMetadata
     */
    private ClassMetadata $metadata;

    /**
     * @param ClassMetadata $metadata
     */
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->metadata->getName();
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return $this->metadata->hasField($fieldName);
    }

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function hasAssociation(string $fieldName): bool
    {
        return $this->metadata->hasAssociation($fieldName);
    }

    /**
     * @param string $fieldName
     *
     * @return array
     *
     * @throws MappingException
     */
    public function getAssociationFiledMapping(string $fieldName): array
    {
        return $this->metadata->getAssociationMapping($fieldName);
    }
}
