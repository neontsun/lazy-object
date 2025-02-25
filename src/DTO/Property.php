<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\DTO;

class Property
{
    /**
     * @param non-empty-string $name
     */
    public function __construct(
        private(set) readonly string $name,
        public mixed $value = null,
    ) {}
}
