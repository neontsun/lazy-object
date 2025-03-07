<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Attribute\Lazy;

final class LazyObjectWithNullDefaultProperty
{
    public ?int $default = null;

    public function __construct(
        private(set) string $name,
        #[Lazy]
        private(set) int $age,
    ) {}
}
