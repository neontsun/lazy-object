<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use InvalidArgumentException;
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Contract\LazyInterface;
use Neontsun\LazyObject\Contract\LazyObjectFactoryInterface;
use Override;
use ReflectionClass;

final readonly class LazyObjectWithImplementedLazyInterface implements LazyInterface
{
	/**
	 * @param list<mixed> $data
	 */
    public function __construct(
        private(set) string $name,
        private(set) string $date,
        #[Lazy]
        private(set) array $data,
    ) {}
	
	/**
	 * @param LazyObjectFactoryInterface $factory
	 * @return LazyGhostBuilderInterface
	 * @throws InvalidArgumentException
	 */
	#[Override]
	public static function lazy(LazyObjectFactoryInterface $factory): LazyGhostBuilderInterface
	{
		return $factory->ghost(self::class);
	}
	
	#[Override]
	public function isUninitialized(): bool
	{
		return new ReflectionClass(self::class)->isUninitializedLazyObject($this);
	}
}
