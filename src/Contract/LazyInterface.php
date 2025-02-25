<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Contract;

use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;

interface LazyInterface
{
    /**
     * @return LazyGhostBuilderInterface<static>
     */
    public static function lazy(LazyObjectFactoryInterface $factory): LazyGhostBuilderInterface;

    /**
     * Checks if the model is a lazy loading model and if the fields that are lazy are not initialized.
     * Returns true if at least one field marked as lazy has been loaded.
     */
    public function isUninitialized(): bool;
}
