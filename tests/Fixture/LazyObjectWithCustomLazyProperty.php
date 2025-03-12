<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Tests\Fixture\Attribute\CustomLazy;

final readonly class LazyObjectWithCustomLazyProperty
{
    /**
     * @param list<mixed> $data
     */
    public function __construct(
        private(set) string $name,
        #[CustomLazy]
        private(set) array $data,
    ) {}
}
