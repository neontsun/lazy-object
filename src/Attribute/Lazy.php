<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Attribute;

use Attribute;
use Neontsun\LazyObject\Contract\Attribute\LazyInterface;

/**
 * The attribute indicates that the property has lazy loading capability.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Lazy implements LazyInterface {}
