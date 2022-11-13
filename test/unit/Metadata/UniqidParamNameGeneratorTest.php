<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Metadata;

use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\UniqidParamNameGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\Metadata\UniqidParamNameGenerator
 */
final class UniqidParamNameGeneratorTest extends TestCase
{
    public function testImplementsParamNameGeneratorInterface(): void
    {
        $paramNameGenerator = new UniqidParamNameGenerator();

        $this->assertInstanceOf(ParamNameGeneratorInterface::class, $paramNameGenerator);
    }

    /**
     * @dataProvider getParamNameIsGeneratedWithParamPrefixData
     */
    public function testParamNameIsGeneratedWithParamPrefix(string $param, string $fieldName, string $alias): void
    {
        $paramNameGenerator = new UniqidParamNameGenerator();

        $this->assertStringStartsWith($param, $paramNameGenerator->generateName($param, $fieldName, $alias));
    }

    /**
     * @return array<int, array<string>>
     */
    public function getParamNameIsGeneratedWithParamPrefixData(): array
    {
        return [
            ['param1', 'foo', 'f'],
            ['test', 'bar', 'b'],
        ];
    }
}
