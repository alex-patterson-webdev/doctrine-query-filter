<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Metadata
 */
interface TypecasterInterface
{
    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param mixed             $value
     * @param string|null       $type
     * @param array<mixed>             $options
     *
     * @return mixed
     *
     * @throws \Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException
     */
    public function typecast(
        MetadataInterface $metadata,
        string $fieldName,
        $value,
        ?string $type = null,
        array $options = []
    );
}
