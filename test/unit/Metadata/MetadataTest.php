<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Exception\MetadataException;
use Arp\DoctrineQueryFilter\Metadata\Metadata;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\MappingException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\Metadata\Metadata
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Metadata
 */
final class MetadataTest extends TestCase
{
    /**
     * @var ClassMetadata<object>&MockObject
     */
    private $classMetadata;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->classMetadata = $this->createMock(ClassMetadata::class);
    }

    /**
     * Assert that the class implements MetadataInterface
     */
    public function testImplementsMetadataInterface(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $this->assertInstanceOf(MetadataInterface::class, $metadata);
    }

    /**
     * Assert that getName() will return the entity class name
     */
    public function testGetNameWillReturnTheEntityClassName(): void
    {
        $className = 'Foo';

        $metadata = new Metadata($this->classMetadata);

        $this->classMetadata->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $this->assertSame($className, $metadata->getName());
    }

    /**
     * Assert a boolean FALSE is returned when calling hasField() with an unknown field
     */
    public function testHasFieldWillReturnFalseForNonExistingField(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $fieldName = 'testFieldName';

        $this->classMetadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(false);

        $this->assertFalse($metadata->hasField($fieldName));
    }

    /**
     * Assert a boolean FALSE is returned when calling hasField() with an unknown field
     */
    public function testHasFieldWillReturnTrueForExistingField(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $fieldName = 'testFieldName';

        $this->classMetadata->expects($this->once())
            ->method('hasField')
            ->with($fieldName)
            ->willReturn(true);

        $this->assertTrue($metadata->hasField($fieldName));
    }

    /**
     * Assert that a failure to load field mapping will raise a MappingException error
     *
     * @throws MetadataException
     */
    public function testGetFieldMappingWillThrowAMetadataExceptionIfTheMappingCannotBeFound(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $className = 'FooClassName';
        $fieldName = 'fooFieldName';

        $exceptionMessage = 'This is a test mapping exception message';
        $exceptionCode = 456;
        $exception = new MappingException($exceptionMessage, $exceptionCode);

        $this->classMetadata->expects($this->once())
            ->method('getFieldMapping')
            ->with($fieldName)
            ->willThrowException($exception);

        $this->classMetadata->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $this->expectException(MetadataException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf(
                'Unable to find field mapping for field \'%s::%s\': %s',
                $className,
                $fieldName,
                $exceptionMessage
            )
        );

        $metadata->getFieldMapping($fieldName);
    }

    /**
     * Assert that mapping data is correctly returned from calls to getFieldMapping()
     *
     * @throws MetadataException
     */
    public function testGetFieldMappingWillReturnMappingData(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $fieldName = 'fooFieldName';
        $mappingData = [
            'name' => $fieldName,
            'type' => 'test',
            'hello' => 123,
        ];

        $this->classMetadata->expects($this->once())
            ->method('getFieldMapping')
            ->with($fieldName)
            ->willReturn($mappingData);

        $this->assertSame($mappingData, $metadata->getFieldMapping($fieldName));
    }

    /**
     * Assert that calls to getFieldType() that fail will throw a MappingException
     *
     * @throws MetadataException
     */
    public function testGetFieldTypeWillThrowMetadataExceptionIfNotMappingTypeCanBeResolved(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $className = 'FooClassName';
        $fieldName = 'fooFieldName';
        $mappingData = [
            'name' => $className,
            // missing the mapping data 'type' will the expected error
        ];

        $this->classMetadata->expects($this->once())
            ->method('getFieldMapping')
            ->with($fieldName)
            ->willReturn($mappingData);

        $this->classMetadata->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $this->expectException(MetadataException::class);
        $this->expectExceptionMessage(
            sprintf('Unable to resolve field data type for \'%s::%s\'', $className, $fieldName)
        );

        $metadata->getFieldType($fieldName);
    }

    /**
     * Assert that calls to getFieldType() will return the mapped column type
     *
     * @throws MetadataException
     */
    public function testGetFieldTypeWillReturnTheMappedType(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $className = 'FooClassName';
        $fieldName = 'fooFieldName';
        $type = 'string';

        $mappingData = [
            'name' => $className,
            'type' => $type
        ];

        $this->classMetadata->expects($this->once())
            ->method('getFieldMapping')
            ->with($fieldName)
            ->willReturn($mappingData);

        $this->assertSame($type, $metadata->getFieldType($fieldName));
    }

    /**
     * Assert a boolean FALSE is returned when calling hasAssociation() with an unknown relationship
     */
    public function testHasAssociationWillReturnFalseForNonExistingField(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $fieldName = 'testFieldName';

        $this->classMetadata->expects($this->once())
            ->method('hasAssociation')
            ->with($fieldName)
            ->willReturn(false);

        $this->assertFalse($metadata->hasAssociation($fieldName));
    }

    /**
     * Assert a boolean TRUE is returned when calling hasAssociation() with an known relationship
     */
    public function testHasAssociationWillReturnTrueForExistingField(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $fieldName = 'testFieldName';

        $this->classMetadata->expects($this->once())
            ->method('hasAssociation')
            ->with($fieldName)
            ->willReturn(true);

        $this->assertTrue($metadata->hasAssociation($fieldName));
    }

    /**
     * Assert that a failure to load association mapping will raise a MappingException error
     *
     * @throws MetadataException
     */
    public function testGetAssociationMappingWillThrowAMetadataExceptionIfTheMappingCannotBeFound(): void
    {
        $metadata = new Metadata($this->classMetadata);

        $className = 'BarClassName';
        $fieldName = 'barFieldName';

        $exceptionCode = 999;
        $exceptionMessage = 'This is a test association mapping exception message';
        $exception = new MappingException($exceptionMessage, $exceptionCode);

        $this->classMetadata->expects($this->once())
            ->method('getAssociationMapping')
            ->with($fieldName)
            ->willThrowException($exception);

        $this->classMetadata->expects($this->once())
            ->method('getName')
            ->willReturn($className);

        $this->expectException(MetadataException::class);
        $this->expectExceptionCode($exceptionCode);
        $this->expectExceptionMessage(
            sprintf(
                'Unable to find association mapping for field \'%s::%s\': %s',
                $className,
                $fieldName,
                $exceptionMessage
            )
        );

        $metadata->getAssociationMapping($fieldName);
    }
}
