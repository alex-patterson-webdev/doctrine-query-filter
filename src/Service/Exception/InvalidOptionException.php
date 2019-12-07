<?php

namespace Arp\DoctrineQueryFilter\Service\Exception;

/**
 * InvalidOptionException
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Service\Exception
 */
class InvalidOptionException extends QueryFilterException
{
    /**
     * missingOptionException
     *
     * @param string          $name
     * @param string          $className
     * @param \Throwable|null $e
     *
     * @return InvalidOptionException
     */
    public static function missingOptionException(string $name, string $className, \Throwable $e = null) : InvalidOptionException
    {
        $exception = isset($e) ? $e            : null;
        $code      = isset($e) ? $e->getCode() : null;

        $message   = sprintf('The \'%s\' option is required for query filter \'%s\'.', $name, $className);

        return new static($message, $code, $exception);
    }

    /**
     * invalidFieldNameException
     *
     * @param string          $name
     * @param string          $entityName
     * @param \Throwable|null $e
     *
     * @return InvalidOptionException
     */
    public static function invalidFieldNameException(string $name, string $entityName, \Throwable $e = null) : InvalidOptionException
    {
        $exception = isset($e) ? $e            : null;
        $code      = isset($e) ? $e->getCode() : null;

        $message   = sprintf('The entity class \'%s\' has no field name or association called \'%s\'.', $entityName, $name);

        return new static($message, $code, $exception);
    }
}