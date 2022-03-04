<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Metadata
 */
final class Metadata implements MetadataInterface
{
    /**
     * @var ClassMetadata<object>
     */
    private ClassMetadata $metadata;

    /**
     * @param ClassMetadata<object> $metadata
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
     * @return array<mixed>
     *
     * @throws MetadataException
     */
    public function getFieldMapping(string $fieldName): array
    {
        try {
            return $this->metadata->getFieldMapping($fieldName);
        } catch (MappingException $e) {
            throw new MetadataException(
                sprintf(
                    'Unable to find field mapping for field \'%s::%s\': %s',
                    $this->getName(),
                    $fieldName,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $fieldName
     *
     * @return string
     *
     * @throws MetadataException
     */
    public function getFieldType(string $fieldName): string
    {
        $type = $this->getFieldMapping($fieldName)['type'] ?? '';

        if (empty($type)) {
            throw new MetadataException(
                sprintf('Unable to resolve field data type for \'%s::%s\'', $this->getName(), $fieldName)
            );
        }

        return $type;
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
     * @return  array<mixed>
     *
     * @throws MetadataException
     */
    public function getAssociationMapping(string $fieldName): array
    {
        try {
            return $this->metadata->getAssociationMapping($fieldName);
        } catch (MappingException $e) {
            throw new MetadataException(
                sprintf(
                    'Unable to find association mapping for field \'%s::%s\': %s',
                    $this->getName(),
                    $fieldName,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }
}
