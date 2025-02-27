<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Attribute;

use Attribute;
use Neontsun\LazyObject\Contract\Attribute\LazyInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Lazy implements LazyInterface {}
