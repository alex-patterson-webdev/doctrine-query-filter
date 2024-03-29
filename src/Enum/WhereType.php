<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

enum WhereType: string
{
    case AND = 'and';
    case OR = 'or';
}
