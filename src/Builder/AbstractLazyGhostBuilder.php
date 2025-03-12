<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Builder;

use Closure;
use InvalidArgumentException;
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\DTO\LazyProperty;
use Neontsun\LazyObject\DTO\ObjectProperties;
use Neontsun\LazyObject\DTO\Property;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Neontsun\LazyObject\Exception\UnexpectedTypeException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;

use function count;
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
     * @var null|class-string
     */
    protected ?string $customLazyAttribute = null;

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
     * @return Closure(T): void
     * @throws LazyObjectException
     */
    protected function getInitializer(ReflectionClass $reflector): Closure
    {
		$initializer = $this->initializer;
		
		$this->checkInitializerInitialize($initializer);
		
        $objectProperties = $this->getObjectProperties($reflector);

        $this->checkGivenAllNotLazyConstructorParameters($objectProperties->nonLazyProperties);
		
        $lazyObjectPropertiesCount = $objectProperties->lazyPropertiesCount;

        return static function(object $class) use ($reflector, $initializer, $lazyObjectPropertiesCount): void {
            $lazyPropertiesIterator = $initializer();
            $lazyProperties = [];

            foreach ($lazyPropertiesIterator as $lazyProperty) {
                if (! $lazyProperty instanceof Property) {
                    throw new UnexpectedTypeException(Property::class, $lazyProperty);
                }

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

            if ($lazyObjectPropertiesCount !== count($lazyProperties)) {
                throw new LazyObjectException(message: 'Not all lazy properties were passed for lazy initialization');
            }

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
	 * @param null|Closure():iterable<Property> $initializer
	 * @throws LazyObjectException
	 * @phpstan-assert !null $initializer
	 */
	private function checkInitializerInitialize(?Closure $initializer): void
	{
		if (null === $initializer) {
			throw new LazyObjectException('Initializer closure must be assigned');
		}
	}
	
    /**
     * We check that all fields that are not marked as lazy are transferred
     *
     * @param list<ReflectionProperty> $nonLazyProperties
     * @throws LazyObjectException
     */
    private function checkGivenAllNotLazyConstructorParameters(array $nonLazyProperties): void
    {
        $nonLazyPropertyNames = array_keys($this->properties);

        foreach ($nonLazyProperties as $nonLazyProperty) {
            if (! in_array($nonLazyProperty->getName(), $nonLazyPropertyNames, true)) {
                throw new LazyObjectException('Not all properties were passed to create the class');
            }
        }
    }

    /**
     * @param ReflectionClass<T> $reflector
     */
    private function getObjectProperties(ReflectionClass $reflector): ObjectProperties
    {
        $nonLazyProperties = [];
        $lazyPropertiesCount = 0;

        foreach ($this->getProperties($reflector) as $property) {
            $propertyAttributes = $property->getAttributes();

            $hasAttribute = false;

            foreach ($propertyAttributes as $propertyAttribute) {
                if (
                    null !== $this->customLazyAttribute
                    && $propertyAttribute->newInstance() instanceof $this->customLazyAttribute
                ) {
                    $hasAttribute = true;

                    break;
                }

                if ($propertyAttribute->newInstance() instanceof Lazy) {
                    $hasAttribute = true;

                    break;
                }
            }

            if ($hasAttribute) {
                ++$lazyPropertiesCount;
            }

            if (! $hasAttribute && ! $property->hasDefaultValue()) {
                $nonLazyProperties[] = $property;
            }
        }

        return new ObjectProperties(
            nonLazyProperties: $nonLazyProperties,
            lazyPropertiesCount: $lazyPropertiesCount,
        );
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
