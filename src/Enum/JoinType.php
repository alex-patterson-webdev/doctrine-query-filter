<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

enum JoinType: string
{
    case INNER = 'INNER';
    case LEFT = 'LEFT';
}
