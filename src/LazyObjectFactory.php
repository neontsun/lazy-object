<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject;

use InvalidArgumentException;
use Neontsun\LazyObject\Builder\LazyGhostBuilder;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Contract\LazyObjectFactoryInterface;
use Override;

final readonly class LazyObjectFactory implements LazyObjectFactoryInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public function ghost(string $class): LazyGhostBuilderInterface
    {
        if (! class_exists($class)) {
            throw new InvalidArgumentException('Expected class-string, but actual type string');
        }

        return new LazyGhostBuilder($class);
    }
}
