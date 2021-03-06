<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DateTime\DateTimeFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\Exception\TypecastException;
use Arp\DoctrineQueryFilter\Filter\Typecaster;
use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \Arp\DoctrineQueryFilter\Filter\Typecaster
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class TypecasterTest extends TestCase
{
    /**
     * @var DateTimeFactoryInterface|MockObject
     */
    private $dateTimeFactory;

    /**
     * @var MetadataInterface|MockObject
     */
    private $metadata;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->dateTimeFactory = $this->createMock(DateTimeFactoryInterface::class);

        $this->metadata = $this->createMock(MetadataInterface::class);
    }

    /**
     * Assert that the class implements TypecasterInterface
     */
    public function testImplementsTypecastInterface(): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory);

        $this->assertInstanceOf(TypecasterInterface::class, $typecaster);
    }

    /**
     * Assert that values passed with a specific $type are not casted and return the same value passed in
     *
     * @param string $type
     * @param mixed  $value
     *
     * @dataProvider getTypecastWillNotCastValueData
     *
     * @throws TypecastException
     */
    public function testTypecastWillNotCastValue(string $type, $value): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory);

        $this->assertSame($value, $typecaster->typecast($this->metadata, 'foo', $value, $type));
    }

    /**
     * @return array
     */
    public function getTypecastWillNotCastValueData(): array
    {
        return [
            ['bigint', 'string#value'],
            ['bigint', 123],
        ];
    }
}
