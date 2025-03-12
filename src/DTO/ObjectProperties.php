<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\DTO;

use ReflectionProperty;

final readonly class ObjectProperties
{
    /**
     * @param list<ReflectionProperty> $nonLazyProperties
     * @param non-negative-int $lazyPropertiesCount
     */
    public function __construct(
        private(set) array $nonLazyProperties,
        private(set) int $lazyPropertiesCount,
    ) {}
}
