<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;

interface TypecasterInterface
{
    /**
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
