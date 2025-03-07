<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests;

use Closure;
use InvalidArgumentException;
use Neontsun\LazyObject\Builder\LazyGhostBuilder;
use Neontsun\LazyObject\DTO\Property;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithImplementedLazyInterface;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithNonConstructorProperties;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithOneLazyProperty;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithStaticPropertyAndDefaultLazyProperty;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithTwoLazyProperty;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\UnknownClassOrInterfaceException;
use ReflectionClass;
use ReflectionException;

#[CoversClass(LazyGhostBuilder::class)]
final class LazyGhostBuilderTest extends TestCase
{
    private LazyObjectFactory $factory;

    public function setUp(): void
    {
        $this->factory = new LazyObjectFactory();
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws GeneratorNotSupportedException
     * @throws ReflectionException
     */
    #[Test]
    public function checkBuilderPropertiesFilled(): void
    {
        $filledBuilder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            });

        $reflector = new ReflectionClass($filledBuilder);

        $propertiesValue = $reflector->getProperty('properties')->getValue($filledBuilder);
        $initializerValue = $reflector->getProperty('initializer')->getValue($filledBuilder);
        $classValue = $reflector->getProperty('class')->getValue($filledBuilder);

        $this->assertIsArray($propertiesValue);
        $this->assertCount(2, $propertiesValue);

        $this->assertInstanceOf(Closure::class, $initializerValue);

        $this->assertEquals(LazyObjectWithOneLazyProperty::class, $classValue);
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     * @throws UnknownClassOrInterfaceException
     */
    #[Test]
    public function checkSuccessBuildGhostWithLazyProperties(): void
    {
        $ghost = $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            })
            ->build();

        $this->assertInstanceOf(LazyObjectWithOneLazyProperty::class, $ghost);
        $this->assertTrue(new ReflectionClass(LazyObjectWithOneLazyProperty::class)->isUninitializedLazyObject($ghost));
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     * @throws UnknownClassOrInterfaceException
     */
    #[Test]
    public function checkSuccessBuildGhostWithSomeLazyProperties(): void
    {
        $ghost = $this->factory->ghost(LazyObjectWithTwoLazyProperty::class)
            ->initializer(static function(): iterable {
                yield from [
                    new Property(
                        name: 'firstData',
                        value: [1, 2, 3],
                    ),
                    new Property(
                        name: 'secondData',
                        value: 123,
                    ),
                ];
            })
            ->build();

        $this->assertInstanceOf(LazyObjectWithTwoLazyProperty::class, $ghost);
        $this->assertTrue(new ReflectionClass(LazyObjectWithTwoLazyProperty::class)->isUninitializedLazyObject($ghost));
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     * @throws UnknownClassOrInterfaceException
     */
    #[Test]
    public function checkSuccessBuildGhostWithNonConstructorProperties(): void
    {
        $ghost = $this->factory->ghost(LazyObjectWithNonConstructorProperties::class)
            ->initializer(static function(): iterable {
                yield from [
                    new Property(
                        name: 'test',
                        value: 'foo',
                    ),
                    new Property(
                        name: 'name',
                        value: 'bar',
                    ),
                    new Property(
                        name: 'age',
                        value: 25,
                    ),
                    new Property(
                        name: 'birthday',
                        value: '2025-01-01 12:00:00',
                    ),
                ];
            })
            ->build();

        $this->assertInstanceOf(LazyObjectWithNonConstructorProperties::class, $ghost);
        $this->assertTrue(new ReflectionClass(LazyObjectWithNonConstructorProperties::class)->isUninitializedLazyObject($ghost));
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     * @throws UnknownClassOrInterfaceException
     */
    #[Test]
    public function checkSuccessBuildGhostWithStaticPropertyAndDefaultLazyProperty(): void
    {
        $ghost = $this->factory->ghost(LazyObjectWithStaticPropertyAndDefaultLazyProperty::class)
			->property('name', 'name')
            ->initializer(static function(): iterable {
                yield from [
                    new Property(
                        name: 'items',
                        value: [1, 2, 3],
                    ),
                ];
            })
            ->build();

        $this->assertInstanceOf(LazyObjectWithStaticPropertyAndDefaultLazyProperty::class, $ghost);
        $this->assertTrue(new ReflectionClass(LazyObjectWithStaticPropertyAndDefaultLazyProperty::class)->isUninitializedLazyObject($ghost));
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function checkInitializedTriggered(): void
    {
        $ghost = $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            })
            ->build();

        $this->assertTrue(new ReflectionClass(LazyObjectWithOneLazyProperty::class)->isUninitializedLazyObject($ghost));
        $foo = $ghost->foo;
        $this->assertFalse(new ReflectionClass(LazyObjectWithOneLazyProperty::class)->isUninitializedLazyObject($ghost));
    }

    /**
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function expectExceptionForBuildWithoutInitializer(): void
    {
        $this->expectException(LazyObjectException::class);

        $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->build();
    }

    /**
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function expectExceptionForBuildWithoutNeededProperties(): void
    {
        $this->expectException(LazyObjectException::class);

        $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            })
            ->build();
    }

    /**
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function expectExceptionForBuildWithInvalidPropertyType(): void
    {
        $this->expectException(LazyObjectException::class);

        $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', [])
            ->property('date', 12_345)
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            })
            ->build();
    }

    /**
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function expectExceptionForBuildWithInvalidLazyPropertyType(): void
    {
        $this->expectException(LazyObjectException::class);

        $ghost = $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: 123,
                );
            })
            ->build();

        $data = $ghost->data;
    }

    /**
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function expectExceptionForBuildWithInvalidLazyProperty(): void
    {
        $this->expectException(LazyObjectException::class);

        $ghost = $this->factory->ghost(LazyObjectWithOneLazyProperty::class)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'invalidData',
                    value: 123,
                );
            })
            ->build();

        $data = $ghost->data;
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws LazyObjectException
     */
    #[Test]
    public function checkSuccessBuildFromLazyInterfaceMethods(): void
    {
        $ghost = LazyObjectWithImplementedLazyInterface::lazy($this->factory)
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(): iterable {
                yield new Property(
                    name: 'data',
                    value: [1, 2, 3],
                );
            })
            ->build();

        $this->assertTrue($ghost->isUninitialized());
        $foo = $ghost->data;
        $this->assertFalse($ghost->isUninitialized());
    }
}
