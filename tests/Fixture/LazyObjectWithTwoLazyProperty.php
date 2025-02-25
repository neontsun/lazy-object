<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Attribute\Lazy;

final readonly class LazyObjectWithTwoLazyProperty
{
	/**
	 * @param list<mixed> $firstData
	 */
    public function __construct(
        #[Lazy]
        private(set) array $firstData,
		#[Lazy]
        private(set) int $secondData,
    ) {}
}
