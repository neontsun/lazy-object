<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests;

use InvalidArgumentException;
use Neontsun\LazyObject\LazyObjectFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(LazyObjectFactory::class)]
final class LazyObjectFactoryTest extends TestCase
{
    private LazyObjectFactory $factory;

    public function setUp(): void
    {
        $this->factory = new LazyObjectFactory();
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
