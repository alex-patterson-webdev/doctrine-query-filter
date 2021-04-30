<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

use Arp\Enum\AbstractEnum;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Enum
 */
final class SortDirection extends AbstractEnum
{
    public const ASC = 'asc';
    public const DESC = 'desc';
}
