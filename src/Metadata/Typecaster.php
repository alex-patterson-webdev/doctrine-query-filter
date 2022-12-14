<?php

declare(strict_types=1);

namespace Arp\DoctrineQueryFilter\Metadata;

use Arp\DateTime\DateTimeFactory;
use Arp\DateTime\DateTimeImmutableFactory;
use Arp\DateTime\Exception\DateTimeFactoryException;
use Arp\DoctrineQueryFilter\Enum\FieldType;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;

final class Typecaster implements TypecasterInterface
{
    public function __construct(
        private readonly DateTimeFactory $dateTimeFactory = new DateTimeFactory(),
        private readonly DateTimeImmutableFactory $dateTimeImmutableFactory = new DateTimeImmutableFactory()
    ) {
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
        mixed $value,
        ?string $type = null,
        array $options = []
    ): mixed {
        if (null === $type) {
            try {
                $type = $metadata->getFieldType($fieldName);
            } catch (MetadataException) {
                return $value;
            }
        }

        try {
            return $this->castValue($value, $type, $options);
        } catch (DateTimeFactoryException $e) {
            throw new TypecastException(
                sprintf('Failed to cast field name \'%s\' to type \'%s\'', $fieldName, $type),
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
    private function castValue(mixed $value, string $type, array $options): mixed
    {
        $castDateTime = !isset($options['cast_date_time']) || $options['cast_date_time'];
        $format = $options['format'] ?? null;

        $fieldType = FieldType::tryFrom($type);

        return match ($fieldType) {
            FieldType::STRING => (string)$value,
            FieldType::INTEGER, FieldType::SMALLINT => (int)$value,
            FieldType::BOOLEAN => (bool)$value,
            FieldType::DECIMAL, FieldType::FLOAT => (float)$value,
            FieldType::DATETIME_MUTABLE => $castDateTime
                ? $this->castDateTime($value, $format ?? 'Y-m-d H:i:s')
                : $value,
            FieldType::DATETIME_IMMUTABLE => $castDateTime
                ? $this->castDateTimeImmutable($value, $format ?? 'Y-m-d H:i:s')
                : $value,
            FieldType::DATE_MUTABLE => $castDateTime
                ? $this->castDate($value, $format ?? 'Y-m-d')
                : $value,
            FieldType::DATE_IMMUTABLE => $castDateTime
                ? $this->castDateImmutable($value, $format ?? 'Y-m-d')
                : $value,
            FieldType::TIME_MUTABLE => $castDateTime
                ? $this->castTime($value, $format ?? 'H:i:s')
                : $value,
            FieldType::TIME_IMMUTABLE => $castDateTime
                ? $this->castTimeImmutable($value, $format ?? 'H:i:s')
                : $value,
            default => $value
        };
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castDateTime(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }
        return $this->dateTimeFactory->createFromFormat($format, $dateTime);
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castDateTimeImmutable(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }
        return $this->dateTimeImmutableFactory->createFromFormat($format, $dateTime);
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castDate(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }

        $value = $this->dateTimeFactory->createFromFormat($format, $dateTime);
        return $this->dateTimeFactory->createFromFormat('Y-m-d H:i:s', $value->format('Y-m-d') . ' 00:00:00');
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castDateImmutable(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }

        $value = $this->dateTimeImmutableFactory->createFromFormat($format, $dateTime);
        return $this->dateTimeImmutableFactory->createFromFormat('Y-m-d H:i:s', $value->format('Y-m-d') . ' 00:00:00');
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castTime(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }
        return $this->dateTimeFactory->createFromFormat($format, $dateTime);
    }

    /**
     * @throws DateTimeFactoryException
     */
    private function castTimeImmutable(\DateTimeImmutable|string $dateTime, string $format): \DateTimeInterface
    {
        if ($dateTime instanceof \DateTimeInterface) {
            return $dateTime;
        }
        return $this->dateTimeImmutableFactory->createFromFormat($format, $dateTime);
    }
}
