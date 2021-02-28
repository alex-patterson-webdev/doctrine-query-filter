<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Constant;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Constant
 */
final class TypecastType
{
    public const INT = 'int';
    public const BOOLEAN = 'boolean';
    public const FLOAT = 'float';
    public const STRING = 'string';
    public const DATE = 'date';
    public const DATE_IMMUTABLE = 'date_immutable';

    /**
     * @return string[]
     */
    public static function getValues(): array
    {
        return [
            self::INT,
            self::BOOLEAN,
            self::FLOAT,
            self::STRING,
            self::DATE,
            self::DATE_IMMUTABLE,
        ];
    }
}
