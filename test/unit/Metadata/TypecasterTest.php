<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Metadata;

use Arp\DateTime\DateTimeFactory;
use Arp\DateTime\DateTimeImmutableFactory;
use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\Exception\TypecastException;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\Metadata\Typecaster;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \Arp\DoctrineQueryFilter\Metadata\Typecaster
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class TypecasterTest extends TestCase
{
    private DateTimeFactory $dateTimeFactory;

    private DateTimeImmutableFactory $dateTimeImmutableFactory;

    /**
     * @var MetadataInterface&MockObject
     */
    private MetadataInterface $metadata;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->dateTimeFactory = new DateTimeFactory();
        $this->dateTimeImmutableFactory = new DateTimeImmutableFactory();
        $this->metadata = $this->createMock(MetadataInterface::class);
    }

    /**
     * Assert that the class implements TypecasterInterface
     */
    public function testImplementsTypecastInterface(): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $this->assertInstanceOf(TypecasterInterface::class, $typecaster);
    }

    /**
     * Assert that calls to typecast() will return the provided value if the type cannot be mapped to a valid
     * type
     *
     * @throws TypecastException
     */
    public function testCastValueWillReturnUnmodifiedValueForInvalidType(): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'FooFieldName';
        $value = 123;

        $exception = new MetadataException('This is a test exception');

        $this->metadata->expects($this->once())
            ->method('getFieldType')
            ->with($fieldName)
            ->willThrowException($exception);

        $this->assertSame($value, $typecaster->typecast($this->metadata, $fieldName, $value));
    }

    /**
     * Assert a TypecastException will be thrown if provided with an invalid type cast value
     *
     * @throws TypecastException
     */
    public function testInvalidCastValueWillThrowTypecastException(): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'FooFieldName';
        $value = 'xyz';
        $type = 'datetime';

        $this->expectException(TypecastException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to cast type \'%s\'', $type)
        );

        $typecaster->typecast($this->metadata, $fieldName, $value, $type);
    }


    /**
     * Assert that typecast will cast values to their expected format
     *
     * @param mixed       $expectedValue
     * @param mixed       $value
     * @param string|null $type
     *
     * @dataProvider getTypecastWillCastSimpleValuesToTheirExpectedValueData
     *
     * @throws TypecastException
     */
    public function testTypecastWillCastSimpleValuesToTheirExpectedValue($expectedValue, $value, ?string $type): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $this->assertSame(
            $expectedValue,
            $typecaster->typecast($this->metadata, 'foo', $value, $type)
        );
    }

    /**
     * @return array<mixed>
     */
    public function getTypecastWillCastSimpleValuesToTheirExpectedValueData(): array
    {
        return [
            [
                'Testing',
                'Testing',
                'string',
            ],
            [
                '123',
                '123',
                'string',
            ],
            [
                100,
                '100',
                'integer',
            ],
            [
                0,
                '0',
                'smallint',
            ],
            [
                true,
                1,
                'boolean',
            ],
            [
                false,
                0,
                'boolean',
            ],
            [
                1.123,
                '1.123',
                'decimal',
            ],
            [
                5.66,
                '5.66',
                'float',
            ],
        ];
    }

    /**
     * @param mixed                $value
     * @param array<string, mixed> $options
     *
     * @throws TypecastException
     *
     * @dataProvider getTypecastWillCastDateData
     */
    public function testTypecastWillCastDate($value, array $options = []): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'foo';
        $castDateTime = !isset($options['cast_date_time']) || $options['cast_date_time'];

        $result = $typecaster->typecast($this->metadata, $fieldName, $value, 'date', $options);
        if (null === $value || !$castDateTime) {
            $this->assertSame($value, $result);
            return;
        }

        $this->assertInstanceOf(\DateTime::class, $result);

        /** @var \DateTime $dateTime */
        $dateTime = \DateTime::createFromFormat($options['format'] ?? 'Y-m-d', $value);

        $this->assertSame(
            $dateTime->format('Y-m-d') . ' 00:00:00',
            $result->format('Y-m-d H:i:s')
        );
    }

    /**
     * @return array<mixed>
     */
    public function getTypecastWillCastDateData(): array
    {
        return [
            [
                '2022-04-14',
                [],
            ],
            [
                '2022-04-14',
                [
                    'cast_date_time' => true,
                ],
            ],
            [
                '2022-04-14',
                [
                    'cast_date_time' => false,
                ],
            ],
            [
                '2022/04/14',
                [
                    'format' => 'Y/m/d',
                ],
            ],
            [
                '2022/04/14 12:33:10',
                [
                    'format' => 'Y/m/d H:i:s',
                ],
            ]
        ];
    }

    /**
     * @param mixed                $value
     * @param array<string, mixed> $options
     *
     * @throws TypecastException
     *
     * @dataProvider getTypecastWillCastDateImmutableData
     */
    public function testTypecastWillCastDateImmutable($value, array $options = []): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'foo';
        $cast = !isset($options['cast_date_time']) || $options['cast_date_time'];

        $result = $typecaster->typecast($this->metadata, $fieldName, $value, 'date_immutable', $options);
        if (!$cast) {
            $this->assertSame($value, $result);
            return;
        }

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);

        /** @var \DateTimeImmutable $dateTime */
        $dateTime = \DateTimeImmutable::createFromFormat($options['format'] ?? 'Y-m-d', $value);

        $this->assertSame(
            $dateTime->format('Y-m-d') . ' 00:00:00',
            $result->format('Y-m-d H:i:s')
        );
    }

    /**
     * @return array<mixed>
     */
    public function getTypecastWillCastDateImmutableData(): array
    {
        return [
            [
                '2022-01-11',
                [],
            ],
            [
                '2022-01-11',
                [
                    'cast_date_time' => true,
                ],
            ],
            [
                '2022-01-11',
                [
                    'cast_date_time' => false,
                ],
            ],
            [
                '2022/01/01',
                [
                    'format' => 'Y/m/d',
                ],
            ],
            [
                '2022/01/11 11:34:12',
                [
                    'format' => 'Y/m/d H:i:s',
                ],
            ]
        ];
    }

    /**
     * @param mixed                $value
     * @param array<string, mixed> $options
     *
     * @throws TypecastException
     *
     * @dataProvider getTypecastWillCastTimeData
     */
    public function testTypecastWillCastTime($value, array $options = []): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'test';

        $this->metadata->expects($this->once())
            ->method('getFieldType')
            ->with($fieldName)
            ->willReturn('time');

        $cast = !isset($options['cast_date_time']) || $options['cast_date_time'];

        $result = $typecaster->typecast($this->metadata, $fieldName, $value, null, $options);
        if (!$cast) {
            $this->assertSame($value, $result);
            return;
        }

        $this->assertInstanceOf(\DateTime::class, $result);

        /** @var \DateTime $dateTime */
        $dateTime = \DateTime::createFromFormat($options['format'] ?? 'H:i:s', $value);

        $this->assertSame($dateTime->format('H:i:s'), $result->format('H:i:s'));
    }

    /**
     * @return array<mixed>
     */
    public function getTypecastWillCastTimeData(): array
    {
        return [
            [
                '13:43:44',
                [],
            ],
            [
                '18:13:12',
                [
                    'cast_date_time' => true,
                ],
            ],
            [
                '07:28:56',
                [
                    'cast_date_time' => false,
                ],
            ],
            [
                '09:21:32',
                [
                    'format' => 'H:i:s',
                ],
            ],
            [
                'Thursday, 11:04:09 AM',
                [
                    'format' => 'l, h:i:s A',
                ],
            ]
        ];
    }

    /**
     * @param mixed                $value
     * @param array<string, mixed> $options
     *
     * @throws TypecastException
     *
     * @dataProvider getTypecastWillCastTimeImmutableData
     */
    public function testTypecastWillCastTimeImmutable($value, array $options = []): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory, $this->dateTimeImmutableFactory);

        $fieldName = 'test';

        $this->metadata->expects($this->once())
            ->method('getFieldType')
            ->with($fieldName)
            ->willReturn('time_immutable');

        $cast = !isset($options['cast_date_time']) || $options['cast_date_time'];

        $result = $typecaster->typecast($this->metadata, $fieldName, $value, null, $options);
        if (!$cast) {
            $this->assertSame($value, $result);
            return;
        }

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);

        /** @var \DateTimeImmutable $dateTime */
        $dateTime = \DateTimeImmutable::createFromFormat($options['format'] ?? 'H:i:s', $value);

        $this->assertSame($dateTime->format('H:i:s'), $result->format('H:i:s'));
    }

    /**
     * @return array<mixed>
     */
    public function getTypecastWillCastTimeImmutableData(): array
    {
        return [
            [
                '13:43:44',
                [],
            ],
            [
                '18:13:12',
                [
                    'cast_date_time' => true,
                ],
            ],
            [
                '07:28:56',
                [
                    'cast_date_time' => false,
                ],
            ],
            [
                '09:21:32',
                [
                    'format' => 'H:i:s',
                ],
            ],
            [
                'Thursday, 11:04:09 AM',
                [
                    'format' => 'l, h:i:s A',
                ],
            ]
        ];
    }
}
