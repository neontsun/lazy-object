<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\DTO;

use ReflectionProperty;

final readonly class LazyProperty
{
    public function __construct(
        private(set) ReflectionProperty $property,
        private(set) mixed $value,
    ) {}
}
