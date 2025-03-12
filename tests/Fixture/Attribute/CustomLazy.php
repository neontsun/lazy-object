<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class CustomLazy {}