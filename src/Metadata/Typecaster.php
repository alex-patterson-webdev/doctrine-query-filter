<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DateTime\DateTimeFactory;
use Arp\DateTime\DateTimeImmutableFactory;
use Arp\DateTime\Exception\DateTimeFactoryException;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;

/**
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package Arp\DoctrineQueryFilter\Metadata
 */
final class Typecaster implements TypecasterInterface
{
    private DateTimeFactory $dateTimeFactory;

    private DateTimeImmutableFactory $dateTimeImmutableFactory;

    public function __construct(
        ?DateTimeFactory $dateTimeFactory = null,
        ?DateTimeImmutableFactory $dateTimeImmutableFactory = null
    ) {
        $this->dateTimeFactory = $dateTimeFactory ?? new DateTimeFactory();
        $this->dateTimeImmutableFactory = $dateTimeImmutableFactory ?? new DateTimeImmutableFactory();
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
        if (null === $type) {
            try {
                $type = $metadata->getFieldType($fieldName);
            } catch (MetadataException $e) {
                return $value;
            }
        }

        try {
            return $this->castValue($value, $type, $options);
        } catch (DateTimeFactoryException $e) {
            throw new TypecastException(
                sprintf('Failed to cast type \'%s\'', $type),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param mixed        $value
     * @param string       $type
     * @param array<mixed> $options
     *
     * @return mixed
     *
     * @throws DateTimeFactoryException
     */
    private function castValue($value, string $type, array $options)
    {
        $castDateTime = !isset($options['cast_date_time']) || $options['cast_date_time'];
        $format = $options['format'] ?? null;

        switch ($type) {
            case 'string':
                return (string)$value;
            case 'integer':
            case 'smallint':
                return (int)$value;
            case 'boolean':
                return (bool)$value;
            case 'decimal':
            case 'float':
                return (float)$value;
            case 'date':
                if ($value && $castDateTime) {
                    $value = $this->dateTimeFactory->createFromFormat($format ?? 'Y-m-d', $value);
                    return $this->dateTimeFactory->createFromFormat(
                        'Y-m-d H:i:s',
                        $value->format('Y-m-d') . ' 00:00:00'
                    );
                }
                break;
            case 'date_immutable':
                if ($value && $castDateTime) {
                    $value = $this->dateTimeImmutableFactory->createFromFormat($format ?? 'Y-m-d', $value);
                    return $this->dateTimeImmutableFactory->createFromFormat(
                        'Y-m-d H:i:s',
                        $value->format('Y-m-d') . ' 00:00:00'
                    );
                }
                break;
            case 'time':
                if ($value && $castDateTime) {
                    return $this->dateTimeFactory->createFromFormat($format ?? 'H:i:s', $value);
                }
                break;
            case 'time_immutable':
                if ($value && $castDateTime) {
                    return $this->dateTimeImmutableFactory->createFromFormat($format ?? 'H:i:s', $value);
                }
                break;
            case 'datetime':
                if ($value && $castDateTime) {
                    return $this->dateTimeFactory->createFromFormat($format ?? 'Y-m-d H:i:s', $value);
                }
                break;
            case 'datetime_immutable':
                if ($value && $castDateTime) {
                    return $this->dateTimeImmutableFactory->createFromFormat($format ?? 'Y-m-d H:i:s', $value);
                }
                break;
        }

        return $value;
    }
}
