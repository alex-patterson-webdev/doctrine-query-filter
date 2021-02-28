<?php

declare(strict_types=1);

namespace ArpTest\DoctrineQueryFilter\Filter;

use Arp\DoctrineQueryFilter\Filter\AbstractFilter;
use Arp\DoctrineQueryFilter\Filter\AndX;
use Arp\DoctrineQueryFilter\Filter\Exception\FilterFactoryException;
use Arp\DoctrineQueryFilter\Filter\FilterFactory;
use Arp\DoctrineQueryFilter\Filter\FilterFactoryInterface;
use Arp\DoctrineQueryFilter\Filter\FilterInterface;
use Arp\DoctrineQueryFilter\Filter\InnerJoin;
use Arp\DoctrineQueryFilter\Filter\IsEqual;
use Arp\DoctrineQueryFilter\Filter\IsGreaterThan;
use Arp\DoctrineQueryFilter\Filter\IsGreaterThanOrEqual;
use Arp\DoctrineQueryFilter\Filter\IsLessThan;
use Arp\DoctrineQueryFilter\Filter\IsLessThanOrEqual;
use Arp\DoctrineQueryFilter\Filter\IsMemberOf;
use Arp\DoctrineQueryFilter\Filter\IsNotEqual;
use Arp\DoctrineQueryFilter\Filter\IsNotNull;
use Arp\DoctrineQueryFilter\Filter\IsNull;
use Arp\DoctrineQueryFilter\Filter\LeftJoin;
use Arp\DoctrineQueryFilter\Filter\OrX;
use Arp\DoctrineQueryFilter\Filter\TypecasterInterface;
use Arp\DoctrineQueryFilter\Metadata\MetadataInterface;
use Arp\DoctrineQueryFilter\QueryBuilderInterface;
use Arp\DoctrineQueryFilter\QueryFilterManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers  \Arp\DoctrineQueryFilter\Filter\FilterFactory
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class FilterFactoryTest extends TestCase
{
    /**
     * @var QueryFilterManagerInterface|MockObject
     */
    private $queryFilterManager;

    /**
     * @var TypecasterInterface|MockObject
     */
    private $typecaster;

    /**
     * Prepare the test case dependencies
     */
    public function setUp(): void
    {
        $this->queryFilterManager = $this->createMock(QueryFilterManagerInterface::class);

        $this->typecaster = $this->createMock(TypecasterInterface::class);
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
     * Assert that if the factory resolves to an invalid filter class a QueryFactoryException will be thrown
     *
     * @param string  $name
     * @param ?string $className
     *
     * @throws FilterFactoryException
     * @dataProvider getCreateWillThrowAFilterFactoryExceptionIfTheResolvedClassNameIsInvalidData
     */
    public function testCreateWillThrowAFilterFactoryExceptionIfTheResolvedClassNameIsInvalid(
        string $name,
        string $className = null
    ): void {
        $classMap = isset($className) ? [$name => $className] : [];

        $factory = new FilterFactory($this->typecaster, $classMap);

        $this->expectException(FilterFactoryException::class);
        $this->expectExceptionMessage(
            sprintf(
                'The query filter \'%s\' must be an object of type \'%s\'; '
                . 'The resolved class \'%s\' is invalid or cannot be found',
                $name,
                FilterInterface::class,
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

    /**
     * @return array
     */
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
     * @param string $expected
     * @param string $name
     * @param array  $options
     * @param array  $classMap
     *
     * @dataProvider getCreateWillReturnFilterInstanceData
     */
    public function testCreateWillReturnFilterInstance(
        string $expected,
        string $name,
        array $options = [],
        array $classMap = []
    ): void {
        $factory = new FilterFactory($this->typecaster, $classMap);

        $queryFilter = $factory->create($this->queryFilterManager, $name, $options);

        /** @noinspection UnnecessaryAssertionInspection */
        $this->assertInstanceOf($expected, $queryFilter);
        $this->assertSame($options, $queryFilter->getOptions());
    }

    /**
     * @return array
     */
    public function getCreateWillReturnFilterInstanceData(): array
    {
        return [
            [AndX::class, 'andx'],
            [AndX::class, AndX::class],

            [OrX::class, 'orx'],
            [OrX::class, OrX::class],

            [IsEqual::class, 'eq'],
            [IsEqual::class, IsEqual::class],

            [IsNotEqual::class, 'neq'],
            [IsNotEqual::class, IsNotEqual::class],

            [IsGreaterThan::class, 'gt'],
            [IsGreaterThan::class, IsGreaterThan::class],

            [IsGreaterThanOrEqual::class, 'gte'],
            [IsGreaterThanOrEqual::class, IsGreaterThanOrEqual::class],

            [IsLessThan::class, 'lt'],
            [IsLessThan::class, IsLessThan::class],

            [IsLessThanOrEqual::class, 'lte'],
            [IsLessThanOrEqual::class, IsLessThanOrEqual::class],

            [IsMemberOf::class, 'memberof'],
            [IsMemberOf::class, IsMemberOf::class],

            [IsNull::class, 'null'],
            [IsNull::class, IsNull::class],

            [IsNotNull::class, 'notnull'],
            [IsNotNull::class, IsNotNull::class],

            [InnerJoin::class, 'innerjoin'],
            [InnerJoin::class, InnerJoin::class],

            [LeftJoin::class, 'leftjoin'],
            [LeftJoin::class, LeftJoin::class],
        ];
    }
}

/**
 * @internal
 *
 * @author  Alex Patterson <alex.patterson.webdev@gmail.com>
 * @package ArpTest\DoctrineQueryFilter\Filter
 */
final class ThrowExceptionInConstructorFilterMock extends AbstractFilter
{
    /**
     * @param QueryFilterManagerInterface $queryFilterManager
     * @param TypecasterInterface         $typecaster
     * @param array                       $options
     *
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection PhpUnusedParameterInspection
     */
    public function __construct(
        QueryFilterManagerInterface $queryFilterManager,
        TypecasterInterface $typecaster,
        array $options = []
    ) {
        throw new \RuntimeException('This is is a test exception');
    }

    /**
     * @param QueryBuilderInterface $queryBuilder
     * @param MetadataInterface     $metadata
     * @param array                 $criteria
     */
    public function filter(QueryBuilderInterface $queryBuilder, MetadataInterface $metadata, array $criteria): void
    {

    }
}
