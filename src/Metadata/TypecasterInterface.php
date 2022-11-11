<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;

interface TypecasterInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param string $fieldName
     * @param mixed $value
     * @param string|null $type
     * @param array<mixed> $options
     *
     * @return mixed
     *
     * @throws TypecastException
     */
    public function typecast(
        MetadataInterface $metadata,
        string $fieldName,
        mixed $value,
        ?string $type = null,
        array $options = []
    ): mixed;
}
