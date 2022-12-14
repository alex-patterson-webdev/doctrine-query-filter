<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

final class UniqidParamNameGenerator implements ParamNameGeneratorInterface
{
    public function generateName(string $param, string $fieldName, string $alias): string
    {
        return uniqid($param, false);
    }
}
