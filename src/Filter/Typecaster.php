<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Filter;

use Arp\DateTime\DateTimeFactory;
use Arp\DateTime\DateTimeFactoryInterface;
use Arp\DateTime\Exception\DateTimeFactoryException;
use Arp\DoctrineQueryFilter\Constant\TypecastType;
use Arp\DoctrineQueryFilter\Filter\Exception\TypecastException;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
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
     * @param DateTimeFactoryInterface|null $dateTimeFactory
     */
    public function __construct(?DateTimeFactoryInterface $dateTimeFactory = null)
    {
        $this->dateTimeFactory = $dateTimeFactory ?? new DateTimeFactory();
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param mixed             $value
     * @param string|null       $type
     * @param array<mixed>      $options
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
        $type = $this->getType($metadata, $fieldName, $type);
        if (null === $type) {
            return $value;
        }

        switch ($type) {
            case 'integer':
            case 'smallint':
                return (int)$value;
            case 'boolean':
                return (bool)$value;
            case 'decimal':
            case 'float':
                return (float)$value;
            case 'string':
                return (string)$value;
        }

        $castDates = !isset($options['cast_dates']) || $options['cast_dates'];
        $dateTypes = [
            'date',
            'date_immutable',
            'datetime',
            'datetime_immutable',
            'time',
        ];

        return ($castDates && in_array($type, $dateTypes))
            ? $this->castDateTime($type, $value, $options['format'] ?? null)
            : $value;
    }

    /**
     * @param string      $type
     * @param mixed       $value
     * @param string|null $format
     *
     * @return \DateTimeInterface
     *
     * @throws TypecastException
     */
    private function castDateTime(string $type, $value, ?string $format = null): \DateTimeInterface
    {
        if ($value instanceof \DateTimeInterface) {
            return $value;
        }

        switch ($type) {
            case 'date':
            case 'date_immutable':
                $value = $this->createDateTime($value, $format ?? 'Y-m-d');

                if ($value instanceof \DateTime) {
                    $value->setTime(0, 0);
                }
                break;

            case 'datetime':
            case 'datetime_immutable':
                $value = $this->createDateTime($value, $format ?? 'Y-m-d H:i:s');
                break;

            case 'time':
                $value = $this->createDateTime($value, $format ?? 'H:i:s');
                break;

            default:
                throw new TypecastException(
                    sprintf('Unable to cast invalid date type \'%s\'', $type)
                );
        }

        return $value;
    }

    /**
     * @param string $spec
     * @param string $format
     *
     * @return \DateTimeInterface
     *
     * @throws TypecastException
     */
    private function createDateTime(string $spec, string $format): \DateTimeInterface
    {
        try {
            return $this->dateTimeFactory->createFromFormat($spec, $format);
        } catch (DateTimeFactoryException $e) {
            throw new TypecastException(
                sprintf('Failed to cast date time to format \'%s\': %s', $format, $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param MetadataInterface $metadata
     * @param string            $fieldName
     * @param string|null       $type
     *
     * @return string|null
     *
     * @throws TypecastException
     */
    private function getType(MetadataInterface $metadata, string $fieldName, ?string $type): ?string
    {
        if (null === $type || !in_array($type, TypecastType::getValues(), true)) {
            return $type;
        }

        if (!$metadata->hasField($fieldName)) {
            throw new TypecastException(
                sprintf('The field \'%s\' does not exist for entity \'%s\'', $fieldName, $metadata->getName())
            );
        }

        try {
            return $metadata->getFieldType($fieldName);
        } catch (MetadataException $e) {
            return null;
        }
    }
}
