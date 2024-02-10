<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter as Filters;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\Filter\FilterFactory;
use Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface;
use Arp\DoctrineQueryFilter\Metadata\ParamNameGeneratorInterface;
use Arp\DoctrineQueryFilter\Metadata\TypecasterInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Arp\DoctrineQueryFilter\Filter\FilterFactory
 */
final class FilterFactoryTest extends TestCase
{
    private QueryFilterManagerInterface&MockObject $queryFilterManager;

    private TypecasterInterface&MockObject $typecaster;

    private ParamNameGeneratorInterface&MockObject $paramNameGenerator;

    /**
     * @var array<string, string>
     */
    private array $defaultClassMap = [
        'eq' => Filters\IsEqual::class,
        'neq' => Filters\IsNotEqual::class,
        'gt' => Filters\IsGreaterThan::class,
        'gte' => Filters\IsGreaterThanOrEqual::class,
        'lt' => Filters\IsLessThan::class,
        'lte' => Filters\IsLessThanOrEqual::class,
        'is_null' => Filters\IsNull::class,
        'not_null' => Filters\IsNotNull::class,
        'member_of' => Filters\IsMemberOf::class,
        'between' => Filters\IsBetween::class,
        'and' => Filters\AndX::class,
        'or' => Filters\OrX::class,
        'left_join' => Filters\LeftJoin::class,
        'inner_join' => Filters\InnerJoin::class,
        'like' => Filters\IsLike::class,
        'not_like' => Filters\IsNotLike::class,
        'in' => Filters\IsIn::class,
        'not_in' => Filters\IsNotIn::class,
        'begins_with' => Filters\BeginsWith::class,
        'ends_with' => Filters\EndsWith::class,
        'empty' => Filters\IsEmpty::class,
    ];

    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManagerInterface::class);
        $this->typecaster = $this->createMock(TypecasterInterface::class);
        $this->paramNameGenerator = $this->createMock(ParamNameGeneratorInterface::class);
    }

    /**
     * Assert that the factory implements FilterFactoryInterface
     */
    public function testImplementsFilterFactory(): void
    {
        $factory = new FilterFactory($this->typecaster);

        $this->assertInstanceOf(FilterFactoryInterface::class, $factory);
    }

    /**
     * Assert the class map can be set and fetched via getClassMap() and setClassMap()
     */
    public function testGetAndSetClassMap(): void
    {
        $factory = new FilterFactory($this->typecaster);

        $this->assertSame($this->defaultClassMap, $factory->getClassMap());

        $replacementClassMap = [
            'eq' => Filters\IsEqual::class,
        ];

        $factory->setClassMap($replacementClassMap);

        $this->assertSame($replacementClassMap, $factory->getClassMap());
    }

    /**
     * Assert that the default class map can be added to
     */
    public function testAddToClassMap(): void
    {
        $factory = new FilterFactory($this->typecaster);

        $factory->addToClassMap('test', Filters\FilterInterface::class);

        $this->assertSame(
            array_merge($this->defaultClassMap, ['test' => Filters\FilterInterface::class]),
            $factory->getClassMap()
        );
    }

    /**
     * Assert that if the factory resolves to an invalid filter class a QueryFactoryException will be thrown
     *
     * @param string $name
     * @param class-string<Filters\FilterInterface>|null $className
     *
     * @throws FilterFactoryException
     * @dataProvider getCreateWillThrowAFilterFactoryExceptionIfTheResolvedClassNameIsInvalidData
     */
    public function testCreateWillThrowAFilterFactoryExceptionIfTheResolvedClassNameIsInvalid(
        string $name,
        string $className = null
    ): void {
        $classMap = isset($className) ? [$name => $className] : [];

        $factory = new FilterFactory($this->typecaster, $this->paramNameGenerator, $classMap);

        $this->expectException(FilterFactoryException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The query filter \'%s\' must be an object of type \'%s\'; '
                . 'The resolved class \'%s\' is invalid or cannot be found',
                $name,
                Filters\FilterInterface::class,
                $className ?? $name
            )
        );

        $factory->create($this->queryFilterManager, $name);
    }

    /**
     * Assert that create() will throw a FilterFactoryException if a valid filter class cannot be created
     *
     * @throws FilterFactoryException
     */
    public function testCreateWillThrowAFilterFactoryExceptionIfTheFilterCannotBeCreated(): void
    {
        $factory = new FilterFactory($this->typecaster);

        // Defined at the bottom of this class
        $name = ThrowExceptionInConstructorFilterMock::class;
        $errorMessage = 'This is is a test exception';

        $this->expectException(FilterFactoryException::class);
        $this->expectExceptionMessage(
            sprintf('Failed to create query filter \'%s\': %s', $name, $errorMessage)
        );

        $factory->create($this->queryFilterManager, $name);
    }

    public function getCreateWillThrowAFilterFactoryExceptionIfTheResolvedClassNameIsInvalidData(): array
    {
        return [
            // Not mapped class does not implement FilterInterface
            [
                \stdClass::class,
            ],

            // Not mapped class does not exist
            [
                'foo_bar',
            ],

            // Mapped class does not exist
            [
                'foo',
                'some_non_existing_class',
            ],

            // Mapped class does not implement FilterInterface
            [
                'foo',
                \stdClass::class,
            ],
        ];
    }

    /**
     * Assert the expected query filter is created using the provided $name and $options and optional $classMap.
     *
     * @param class-string $expected
     *
     * @dataProvider getCreateWillReturnFilterInstanceData
     *
     * @throws FilterFactoryException
     */
    public function testCreateWillReturnFilterInstance(
        string $expected,
        string $name,
        array $options = [],
        array $classMap = []
    ): void {
        $factory = new FilterFactory($this->typecaster, $this->paramNameGenerator, $classMap);

        $queryFilter = $factory->create($this->queryFilterManager, $name, $options);

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf($expected, $queryFilter);
    }

    public function getCreateWillReturnFilterInstanceData(): array
    {
        return [
            [Filters\AndX::class, 'and'],
            [Filters\AndX::class, Filters\AndX::class],

            [Filters\OrX::class, 'or'],
            [Filters\OrX::class, Filters\OrX::class],

            [Filters\IsEqual::class, 'eq'],
            [Filters\IsEqual::class, Filters\IsEqual::class],

            [Filters\IsNotEqual::class, 'neq'],
            [Filters\IsNotEqual::class, Filters\IsNotEqual::class],

            [Filters\IsGreaterThan::class, 'gt'],
            [Filters\IsGreaterThan::class, Filters\IsGreaterThan::class],

            [Filters\IsGreaterThanOrEqual::class, 'gte'],
            [Filters\IsGreaterThanOrEqual::class, Filters\IsGreaterThanOrEqual::class],

            [Filters\IsLessThan::class, 'lt'],
            [Filters\IsLessThan::class, Filters\IsLessThan::class],

            [Filters\IsLessThanOrEqual::class, 'lte'],
            [Filters\IsLessThanOrEqual::class, Filters\IsLessThanOrEqual::class],

            [Filters\IsMemberOf::class, 'member_of'],
            [Filters\IsMemberOf::class, Filters\IsMemberOf::class],

            [Filters\IsNull::class, 'is_null'],
            [Filters\IsNull::class, Filters\IsNull::class],

            [Filters\IsNotNull::class, 'not_null'],
            [Filters\IsNotNull::class, Filters\IsNotNull::class],

            [Filters\InnerJoin::class, 'inner_join'],
            [Filters\InnerJoin::class, Filters\InnerJoin::class],

            [Filters\LeftJoin::class, 'left_join'],
            [Filters\LeftJoin::class, Filters\LeftJoin::class],
        ];
    }
}
