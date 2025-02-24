<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Contract;

use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;

interface LazyObjectFactoryInterface
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @return LazyGhostBuilderInterface<T>
     */
    public function ghost(string $class): LazyGhostBuilderInterface;
}
