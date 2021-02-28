<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\Metadata;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
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
     * @var ClassMetadata|MockObject
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
}
