<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Contract\Builder;

use Closure;
use Neontsun\LazyObject\DTO\Property;
use Neontsun\LazyObject\Exception\LazyObjectException;

/**
 * @template T of object
 */
interface LazyGhostBuilderInterface
{
    /**
     * @param class-string $customLazyAttribute
     */
    public function setCustomLazyAttribute(string $customLazyAttribute): void;

    /**
     * Set up an initializer that will be called when lazy fields are accessed.
     *
     * @param Closure():iterable<Property> $closure
     * @return self<T>
     */
    public function initializer(Closure $closure): self;

    /**
     * Doesn't mark the field in any way, but simply fills it with the transmitted data.
     * When accessing a field, it does NOT initialize other fields.
     *
     * @param non-empty-string $property
     * @return self<T>
     */
    public function property(string $property, mixed $value): self;

    /**
     * @return T
     * @throws LazyObjectException
     */
    public function build();
}
