<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Tests\Fixture\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Lazy extends \Neontsun\LazyObject\Attribute\Lazy {}
