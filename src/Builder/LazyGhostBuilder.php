<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Builder;

use Closure;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Override;
use ReflectionClass;
use Throwable;

/**
 * @template T of object
 * @extends AbstractLazyGhostBuilder<T>
 * @implements LazyGhostBuilderInterface<T>
 */
class LazyGhostBuilder extends AbstractLazyGhostBuilder implements LazyGhostBuilderInterface
{
    /**
     * @inheritDoc
     * @return self<T>
     */
    #[Override]
    public function setCustomLazyAttribute(string $customLazyAttribute): self
    {
        $this->customLazyAttribute = $customLazyAttribute;

        return $this;
    }

    /**
     * @inheritDoc
     * @return self<T>
     */
    #[Override]
    public function initializer(Closure $closure): self
    {
        $this->initializer = $closure;

        return $this;
    }

    /**
     * @inheritDoc
     * @return self<T>
     */
    #[Override]
    public function property(string $property, mixed $value): self
    {
        $this->properties[$property] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function build(): object
    {
        try {
            $reflectionClass = new ReflectionClass($this->class);

            /**
             * We create a closure-initializer for deferred class creation.
             * If the builder has class properties without deferred initialization,
             * then we set them in the constructor from the class itself.
             * There, in turn, they appear from the code below,
             * where we put them ourselves and marked these properties as properties without deferred initialization.
             */
            $ghost = $reflectionClass->newLazyGhost(
                initializer: $this->getInitializer($reflectionClass),
            );

            /**
             * We go through the properties and set them in the object, while removing the mark that they are lazy
             */
            foreach ($this->properties as $property => $value) {
                try {
                    $reflectionClass->getProperty($property)->setRawValueWithoutLazyInitialization($ghost, $value);
                } catch (Throwable $e) {
                    throw new LazyObjectException(
                        message: 'The type of the non-lazy property does not match the type in the class constructor',
                        previous: $e,
                    );
                }
            }

            return $ghost;
        } catch (Throwable $e) {
            throw new LazyObjectException(
                message: $e->getMessage(),
                code: $e->getCode(),
                previous: $e,
            );
        }
    }
}
