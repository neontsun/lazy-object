<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Builder;

use Closure;
use InvalidArgumentException;
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\DTO\LazyProperty;
use Neontsun\LazyObject\DTO\Property;
use Neontsun\LazyObject\Exception\LazyObjectException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;

use function in_array;

/**
 * @template T of object
 */
abstract class AbstractLazyGhostBuilder
{
    /**
     * @var class-string<T> $class
     */
    protected readonly string $class;

    /**
     * @var null|Closure():iterable<Property>
     */
    protected ?Closure $initializer = null;

    /**
     * @var array<non-empty-string, mixed>
     */
    protected array $properties = [];

    /**
     * @param class-string<T> $class
     * @throws InvalidArgumentException
     */
    public function __construct(string $class)
    {
        if (! class_exists($class)) {
            throw new InvalidArgumentException('Expected class-string, but actual type string');
        }

        $this->class = $class;
    }

    /**
     * @param ReflectionClass<T> $reflector
     * @throws LazyObjectException
     * @phpstan-assert !null $this->initializer
     */
    protected function checksBeforeBuild(ReflectionClass $reflector): void
    {
        if (null === $this->initializer) {
            throw new LazyObjectException('Initializer closure must be assigned');
        }

        $this->checkGivenAllNotLazyConstructorParameters($reflector);
    }

    /**
     * @param ReflectionClass<T> $reflector
     * @return Closure(T): void
     * @throws LazyObjectException
     */
    protected function getInitializer(ReflectionClass $reflector): Closure
    {
        $this->checksBeforeBuild($reflector);

        $lazyPropertiesIterator = ($this->initializer)();
        $lazyProperties = [];

        foreach ($lazyPropertiesIterator as $lazyProperty) {
            try {
                $lazyProperties[] = new LazyProperty(
                    property: $reflector->getProperty($lazyProperty->name),
                    value: $lazyProperty->value,
                );
            } catch (ReflectionException $e) {
                throw new LazyObjectException(
                    message: 'One of the properties returned from the closure was not found',
                    previous: $e,
                );
            }
        }

        return static function(object $class) use ($lazyProperties): void {
            try {
                foreach ($lazyProperties as $lazyProperty) {
                    $lazyProperty->property->setRawValue($class, $lazyProperty->value);
                }
            } catch (Throwable $e) {
                throw new LazyObjectException(
                    message: 'The properties passed to create the deferred object do not match the types declared in the class',
                    previous: $e,
                );
            }
        };
    }

    /**
     * We check that all fields that are not marked as lazy are transferred
     *
     * @param ReflectionClass<T> $reflector
     * @throws LazyObjectException
     */
    private function checkGivenAllNotLazyConstructorParameters(ReflectionClass $reflector): void
    {
        $nonLazyPropertyNames = array_keys($this->properties);

        foreach ($this->getNonLazyProperties($reflector) as $nonLazyProperty) {
            if (! in_array($nonLazyProperty->getName(), $nonLazyPropertyNames, true)) {
                throw new LazyObjectException('Not all properties were passed to create the class');
            }
        }
    }

    /**
     * @param ReflectionClass<T> $reflector
     * @return iterable<ReflectionProperty>
     */
    private function getNonLazyProperties(ReflectionClass $reflector): iterable
    {
        foreach ($this->getProperties($reflector) as $property) {
			$propertyAttributes = $property->getAttributes();
			
			$hasAttribute = false;
			
			foreach ($propertyAttributes as $propertyAttribute) {
				if ($propertyAttribute->newInstance() instanceof Lazy) {
					$hasAttribute = true;
					
					break;
				}
			}
			
            if (! $hasAttribute && ! $property->hasDefaultValue()) {
                yield $property;
            }
        }
    }

    /**
     * @param ReflectionClass<T> $reflector
     * @return iterable<ReflectionProperty>
     */
    private function getProperties(ReflectionClass $reflector): iterable
    {
        yield from $reflector->getProperties();
    }
}
