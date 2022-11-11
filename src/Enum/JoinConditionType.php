<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

enum JoinConditionType: string
{
    case ON = 'ON';
    case WITH = 'WITH';
}
