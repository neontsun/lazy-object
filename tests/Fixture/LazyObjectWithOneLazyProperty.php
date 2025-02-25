<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Attribute\Lazy;

final readonly class LazyObjectWithOneLazyProperty
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
}
