<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

enum OrderByDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
