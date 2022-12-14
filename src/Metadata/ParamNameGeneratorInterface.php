<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

interface ParamNameGeneratorInterface
{
    public function generateName(string $param, string $fieldName, string $alias): string;
}
