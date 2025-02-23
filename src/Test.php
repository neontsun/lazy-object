<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject;

class Test
{
	/**
	 * @param non-empty-string $a
	 */
	public function a(string $a): void
	{
		if ('' === $a) {
			throw new \Exception();
		}
	}
}