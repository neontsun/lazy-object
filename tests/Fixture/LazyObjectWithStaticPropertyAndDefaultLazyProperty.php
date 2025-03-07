<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Tests\Fixture\Attribute\Lazy;
use stdClass;

abstract class ParentLazyObjectWithStaticPropertyAndDefaultLazyProperty
{
    protected(set) string $class;

    /**
     * @param string $name
     * @param list<int> $items
     */
    public function __construct(
        protected(set) string $name,
        #[Lazy]
        protected(set) array $items = [],
    ) {}
}

final class LazyObjectWithStaticPropertyAndDefaultLazyProperty extends ParentLazyObjectWithStaticPropertyAndDefaultLazyProperty
{
    protected(set) string $class = stdClass::class;
}
