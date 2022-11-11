<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;

interface MetadataInterface
{
    public function getName(): string;

    public function hasField(string $fieldName): bool;

    /**
     * @param string $fieldName
     *
     * @return array<mixed>
     *
     * @throws MetadataException
     */
    public function getFieldMapping(string $fieldName): array;

    /**
     * @throws MetadataException
     */
    public function getFieldType(string $fieldName): string;

    public function hasAssociation(string $fieldName): bool;

    /**
     * @param string $fieldName
     *
     * @return array<mixed>
     *
     * @throws MetadataException
     */
    public function getAssociationMapping(string $fieldName): array;
}
