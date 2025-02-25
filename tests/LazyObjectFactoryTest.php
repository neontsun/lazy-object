<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests;

use InvalidArgumentException;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\Tests\Fixture\LazyObjectWithOneLazyProperty;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\UnknownClassOrInterfaceException;

#[CoversClass(LazyObjectFactory::class)]
final class LazyObjectFactoryTest extends TestCase
{
    private LazyObjectFactory $factory;

    public function setUp(): void
    {
        $this->factory = new LazyObjectFactory();
    }

    /**
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws UnknownClassOrInterfaceException
     * @throws InvalidArgumentException
     */
    #[Test]
    public function successCreateGhostBuilder(): void
    {
        $builder = $this->factory->ghost(LazyObjectWithOneLazyProperty::class);

        self::assertInstanceOf(LazyGhostBuilderInterface::class, $builder);
    }
	
	/**
	 * @throws InvalidArgumentException
	 */
    #[Test]
    public function exceptInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

		/** @phpstan-ignore-next-line */
        $this->factory->ghost('class-string');
    }
}
