<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;

final class Metadata implements MetadataInterface
{
    /**
     * @param ClassMetadata<object> $metadata
     */
    public function __construct(private readonly ClassMetadata $metadata)
    {
    }

    public function getName(): string
    {
        return $this->metadata->getName();
    }

    public function hasField(string $fieldName): bool
    {
        return $this->metadata->hasField($fieldName);
    }

    /**
     * @throws MetadataException
     */
    public function getFieldMapping(string $fieldName): array
    {
        try {
            return $this->metadata->getFieldMapping($fieldName);
        } catch (MappingException $e) {
            throw new MetadataException(
                sprintf('Unable to find field mapping for field \'%s::%s\'', $this->getName(), $fieldName),
                $e->getCode(),
                $e
            );
        }
    }

    /**
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

    public function hasAssociation(string $fieldName): bool
    {
        return $this->metadata->hasAssociation($fieldName);
    }

    /**
     * @throws MetadataException
     */
    public function getAssociationMapping(string $fieldName): array
    {
        try {
            return $this->metadata->getAssociationMapping($fieldName);
        } catch (MappingException $e) {
            throw new MetadataException(
                sprintf('Unable to find association mapping for field \'%s::%s\'', $this->getName(), $fieldName),
                $e->getCode(),
                $e
            );
        }
    }
}
