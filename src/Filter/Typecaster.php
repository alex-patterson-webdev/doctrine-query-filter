<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DateTime\DateTimeFactoryInterface;
use Arp\DateTime\Exception\DateTimeFactoryException;
use Arp\DoctrineQueryFilter\Constant\TypecastType;
use Arp\DoctrineQueryFilter\Filter\Exception\TypecastException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Filter
 */
final class Typecaster implements TypecasterInterface
{
    /**
     * @var DateTimeFactoryInterface
     */
    private DateTimeFactoryInterface $dateTimeFactory;

    /**
     * @var array
     */
    private array $options;

    /**
     * @param DateTimeFactoryInterface $dateTimeFactory
     * @param array                    $options
     */
    public function __construct(DateTimeFactoryInterface $dateTimeFactory, array $options = [])
    {
        $this->dateTimeFactory = $dateTimeFactory;
        $this->options = $options;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param mixed             $value
     * @param string|null       $type
     * @param array             $options
     *
     * @return mixed
     *
     * @throws TypecastException
     */
    public function typecast(
        MetadataInterface $metadata,
        string $fieldName,
        $value,
        ?string $type = null,
        array $options = []
    ) {
        if (null === $type || !in_array($type, TypecastType::getValues(), true)) {
            if (!$metadata->hasField($fieldName)) {
                throw new TypecastException(
                    sprintf('Failed to cast date time to format \'%s\': %s', $format, $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
            $type = $metadata->getFieldType($fieldName);
        }

        $castDates = isset($options['cast_dates']) ? (bool)$options['cast_dates'] : true;
        switch ($type) {
            case 'integer':
            case 'smallint':
            case 'bigint': // Remove?
                $value = (int)$value;
            break;

            case 'boolean':
                $value = (bool)$value;
            break;

            case 'decimal':
            case 'float':
                $value = (float)$value;
            break;

            case 'string':
                $value = (string)$value;
            break;

            case 'date':
            case 'date_immutable':
                $value = $castDates
                    ? $this->castDateTime($value, $options['format'] ?? 'Y-m-d')
                    : $value;
                
                if ($value instanceof \DateTime) {
                    $value->setTime(0, 0, 0);
                }
            break;

            case 'datetime':
            case 'datetime_immutable':
                $value = $castDates
                    ? $this->castDateTime($value, $options['format'] ?? 'Y-m-d H:i:s')
                    : $value;
            break;

            case 'time':
                $value = $castDates
                    ? $this->castDateTime($value, $options['format'] ?? 'H:i:s')
                    : $value;
            break;
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param string $format
     *
     * @return \DateTimeInterface
     *
     * @throws TypecastException
     */
    private function castDateTime($value, string $format): \DateTimeInterface
    {
        try {
            return $this->dateTimeFactory->createFromFormat($value, $format);
        } catch (DateTimeFactoryException $e) {
            throw new TypecastException(
                sprintf('Failed to cast date time to format \'%s\': %s', $format, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }
}
