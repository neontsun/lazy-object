<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject;

use Neontsun\LazyObject\Builder\LazyGhostBuilder;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Contract\LazyObjectFactoryInterface;
use Override;

final readonly class LazyObjectFactory implements LazyObjectFactoryInterface
{
    #[Override]
    public function ghost(string $class): LazyGhostBuilderInterface
    {
        return new LazyGhostBuilder($class);
    }
}
