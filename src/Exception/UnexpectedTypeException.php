<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Exception;

use RuntimeException;

use function sprintf;

class UnexpectedTypeException extends RuntimeException
{
    public function __construct(string $expected, mixed $actual)
    {
        parent::__construct(sprintf('Expected type %s, but actual type %s', $expected, get_debug_type($actual)));
    }
}
