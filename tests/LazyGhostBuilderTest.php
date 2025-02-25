<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests;

use Closure;
use InvalidArgumentException;
use Neontsun\LazyObject\Builder\LazyGhostBuilder;
use Neontsun\LazyObject\DTO\Property;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithOneLazyProperty;
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
        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $filledBuilder = $builder
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(Property $data): void {
                $data->value = [1, 2, 3];
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
        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $ghost = $builder
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(Property $data): void {
                $data->value = [1, 2, 3];
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
     */
    #[Test]
    public function checkInitializedTriggered(): void
    {
        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $ghost = $builder
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(Property $data): void {
                $data->value = [1, 2, 3];
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

        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $builder
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

        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $builder
            ->initializer(static function(Property $data): void {
                $data->value = [1, 2, 3];
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

        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $builder
            ->property('name', [])
            ->property('date', 12_345)
            ->initializer(static function(Property $data): void {
                $data->value = [1, 2, 3];
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

        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        $ghost = $builder
            ->property('name', 'name')
            ->property('date', '2025-12-01')
            ->initializer(static function(Property $data): void {
                $data->value = 123;
            })
            ->build();

        $data = $ghost->data;
    }
}
