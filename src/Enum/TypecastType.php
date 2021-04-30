<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Enum;

use Arp\Enum\AbstractEnum;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Enum
 */
final class TypecastType extends AbstractEnum
{
    public const INT = 'int';
    public const BOOLEAN = 'boolean';
    public const FLOAT = 'float';
    public const STRING = 'string';
    public const DATE = 'date';
    public const DATE_IMMUTABLE = 'date_immutable';
}
