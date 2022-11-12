<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

final class ArrayMapNameGenerator implements ParamNameGeneratorInterface
{
    /**
     * @param array<string, string> $mappings
     */
    public function __construct(private readonly array $mappings)
    {
    }

    public function generateName(string $param, string $fieldName, string $alias): string
    {
        if (isset($this->mappings[$param])) {
            return $this->mappings[$param];
        }

        if (isset($this->mappings[$fieldName])) {
            return $this->mappings[$fieldName];
        }

        if (isset($this->mappings[$alias])) {
            return $this->mappings[$alias];
        }

        return $param;
    }
}
