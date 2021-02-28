<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DateTime\DateTimeFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\Typecaster;
use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
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
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->dateTimeFactory = $this->createMock(DateTimeFactoryInterface::class);
    }

    /**
     * Assert that the class implements TypecasterInterface
     */
    public function testImplementsTypecastInterface(): void
    {
        $typecaster = new Typecaster($this->dateTimeFactory);

        $this->assertInstanceOf(TypecasterInterface::class, $typecaster);
    }
}
