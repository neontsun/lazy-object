<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture;

use Neontsun\LazyObject\Attribute\Lazy;

class A
{
    #[Lazy]
    protected(set) string $test;

    protected(set) int $test2;
}

final class LazyObjectWithNonConstructorProperties extends A
{
    #[Lazy]
    private(set) string $name;
	
    #[Lazy]
    private(set) int $age;

    public function __construct(
        #[Lazy]
        private(set) readonly string $birthday,
    ) {}
}
