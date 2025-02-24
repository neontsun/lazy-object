<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Builder;

use Closure;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Entity\LazyGroup;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Override;
use ReflectionClass;
use Throwable;

use function array_key_exists;

/**
 * @template T of object
 * @extends AbstractLazyGhostBuilder<T>
 * @implements LazyGhostBuilderInterface<T>
 */
final class LazyGhostBuilder extends AbstractLazyGhostBuilder implements LazyGhostBuilderInterface
{
    /**
     * @inheritDoc
     * @return self<T>
     */
    #[Override]
    public function lazyProperty(string $property, Closure $lazy): self
    {
        $this->checkLazyPropertiesHasLazyAttribute([$property]);

        $this->lazyProperties[$property] = $lazy;

        return $this;
    }

    /**
     * @inheritDoc
     * @return self<T>
     */
    #[Override]
    public function lazyGroupProperties(array $properties, Closure $lazy): self
    {
        $this->checkLazyPropertiesHasLazyAttribute($properties);

        $this->lazyGroupProperties[] = new LazyGroup(
            id: (string) time(),
            properties: $properties,
            closure: $lazy,
        );

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
        $reflectionClass = new ReflectionClass($this->class);

        $constructorParameterNames = $this->constructorPropertiesNames($reflectionClass);

        /**
         * ATTENTION:
         * Проверяем, что кол-во параметров в конструкторе класса совпадает с суммарным
         * кол-вом свойств в двух наших массивах @see self::properties, self::lazyProperties
         */
        if (! $this->checkGivenAllClassConstructorParameters($constructorParameterNames)) {
            throw new LazyObjectException('Переданы не все свойства, которые необходимы для создания класса');
        }

        /**
         * Исключаем параметры, которые будут задаваться вне инициализатора
         */
        $constructorParameterNamesForInitializer = [];

        foreach ($constructorParameterNames as $property) {
            if (array_key_exists($property, $this->properties)) {
                continue;
            }

            $constructorParameterNamesForInitializer[] = $property;
        }

        /**
         * Создаем замыкание-инициализатор для отложенного создания класса
         * Если в билдере есть свойства класса без отложенной инициализации, то
         * устанавливаем их в конструкторе из самого класса
         * Там, в свою очередь, они появляются из кода ниже, куда мы их сами положили и пометили
         * эти свойства как свойства без отложенной инициализации
         */
        $ghost = $reflectionClass->newLazyGhost(
            initializer: $this->initializer($constructorParameterNamesForInitializer),
        );

        /**
         * Проходимся по свойствам и устанавливаем их в объект, снимая при этом пометку о том,
         * что они являются отложенными
         */
        foreach ($this->properties as $property => $value) {
            try {
                $reflectionClass->getProperty($property)->setRawValueWithoutLazyInitialization($ghost, $value);
            } catch (Throwable) {
                throw new LazyObjectException('Тип неотложенного свойства не соответствует типу в конструкторе класса');
            }
        }

        return $ghost;
    }
}
