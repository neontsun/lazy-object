<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Entity;

use Closure;

use function in_array;

final readonly class LazyGroup
{
    /**
     * @param non-empty-string $id
     * @param list<non-empty-string> $properties
     * @param Closure():mixed $closure
     */
    public function __construct(
        private(set) string $id,
        private(set) array $properties,
        private(set) Closure $closure,
    ) {}

    public function hasProperty(string $property): bool
    {
        return in_array($property, $this->properties, true);
    }
}
