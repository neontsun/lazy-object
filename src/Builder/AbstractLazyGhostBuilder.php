<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Builder;

use Closure;
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\Entity\LazyGroup;
use Neontsun\LazyObject\Exception\LazyObjectException;
use Neontsun\ReadAttribute\ReadAttribute;
use ReflectionClass;
use ReflectionException;

use function count;
use function in_array;
use function is_array;

/**
 * @template T of object
 */
abstract class AbstractLazyGhostBuilder
{
    use ReadAttribute;

    /**
     * @var array<non-empty-string, Closure():mixed>
     */
    protected array $lazyProperties = [];

    /**
     * @var array<non-empty-string, mixed>
     */
    protected array $properties = [];

    /**
     * @var list<LazyGroup>
     */
    protected array $lazyGroupProperties = [];

    /**
     * @param class-string<T> $class
     */
    public function __construct(
        protected readonly string $class,
    ) {}

    /**
     * @param non-empty-list<non-empty-string> $properties
     * @throws LazyObjectException
     */
    protected function checkLazyPropertiesHasLazyAttribute(array $properties): void
    {
        try {
            $have = $this->propertiesHaveAnAttribute($this->class, $properties, Lazy::class);
        } catch (ReflectionException) {
            throw new LazyObjectException('Передан массив свойств, одно или несколько из которых нет в классе');
        }

        if (! $have) {
            throw new LazyObjectException('Не у всех свойств класса, переданных для отложенной инициализации, стоит атрибут ' . Lazy::class);
        }
    }

    /**
     * @param ReflectionClass<T> $reflector
     * @return list<non-empty-string>
     * @throws LazyObjectException
     */
    protected function constructorPropertiesNames(ReflectionClass $reflector): array
    {
        $constructor = $reflector->getConstructor();

        if (null === $constructor) {
            throw new LazyObjectException('У переданного класса нет конструктора');
        }

        $reflectionParameters = $constructor->getParameters();
        $properties = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            if (! $reflectionParameter->isPromoted()) {
                throw new LazyObjectException('Параметр конструктора не продвинут до свойства класса');
            }

            $properties[] = $reflectionParameter->getName();
        }

        return $properties;
    }

    /**
     * @param list<non-empty-string> $constructorParameterNames
     */
    protected function checkGivenAllClassConstructorParameters(array $constructorParameterNames): bool
    {
        $lazyGroupsKeys = (function(): array {
            $keys = [];

            foreach ($this->lazyGroupProperties as $lazyGroupProperty) {
                $keys = array_merge($keys, $lazyGroupProperty->properties);
            }

            return $keys;
        })();

        $separatedCount = count($this->lazyProperties) + count($this->properties) + count($lazyGroupsKeys);
        $constructorParameterCount = count($constructorParameterNames);

        /**
         * Если кол-во параметров в конструкторе не совпадает с кол-вом в переданных массивах
         */
        if ($constructorParameterCount !== $separatedCount) {
            return false;
        }

        $mergedKeys = array_merge(
            array_keys($this->lazyProperties),
            array_keys($this->properties),
            array_unique($lazyGroupsKeys),
        );
        $mergedKeys = array_unique($mergedKeys);

        $mergedCount = count($mergedKeys);

        /**
         * Если кол-во свойств в переданных двух массивах сложенных раздельно
         * не равно кол-ву, посчитанному через слияния двух массивов
         * Это могло произойти, если в массивах есть пересекающиеся ключи
         */
        if ($separatedCount !== $mergedCount) {
            return false;
        }

        /**
         * Получаем слияние ключи свойств из двух массивов и находим пересечение с параметрами конструктора
         * Если их кол-во совпадает, то сравниваем массив ключей и массив параметров конструктора
         */
        $intersect = array_intersect($constructorParameterNames, $mergedKeys);

        if ($constructorParameterCount !== count($intersect)) {
            return false;
        }

        sort($constructorParameterNames);
        sort($intersect);

        return $constructorParameterNames === $intersect;
    }

    /**
     * @param list<non-empty-string> $constructorParameterNames
     * @return Closure(T): void
     */
    protected function initializer(array $constructorParameterNames): Closure
    {
        return function(object $class) use ($constructorParameterNames): void {
            $constructorParameters = [];
            $initializerGroups = [];

            foreach ($constructorParameterNames as $parameterName) {
                $group = $this->getGroupClosureByPropertyName($parameterName);

                if (
                    ! isset($this->lazyProperties[$parameterName])
                    && null === $group
                ) {
                    throw new LazyObjectException('Переданы не все аргументы конструктора для создания объекта');
                }

                if (isset($this->lazyProperties[$parameterName])) {
                    $lazyCallback = $this->lazyProperties[$parameterName];

                    $constructorParameters[$parameterName] = $lazyCallback();

                    continue;
                }

                if (null === $group) {
                    throw new LazyObjectException(
                        'Достигнут нереалистичный сценарий, когда прошло условие нахождения параметра конструктора во всех массивах, а группа не нашлась',
                    );
                }

                if (in_array($group->id, $initializerGroups, true)) {
                    continue;
                }

                $groupCallbackResult = ($group->closure)();

                if (! is_array($groupCallbackResult)) {
                    throw new LazyObjectException('Замыкание получения данных для группы свойств возвращает не массив');
                }

                if (count($groupCallbackResult) !== count($group->properties)) {
                    throw new LazyObjectException('Замыкание получения данных вернула массив данных не соответствующий списку свойств');
                }

                $callbackResultKeys = array_keys($groupCallbackResult);
                $groupProperties = $group->properties;

                sort($callbackResultKeys);
                sort($groupProperties);

                if ($callbackResultKeys !== $groupProperties) {
                    throw new LazyObjectException('Замыкание получения данных вернула массив данных не соответствующий списку свойств');
                }

                $initializerGroups[] = $group->id;

                foreach ($callbackResultKeys as $callbackResultKey) {
                    $constructorParameters[$callbackResultKey] = $groupCallbackResult[$callbackResultKey];
                }
            }

            try {
                $reflectionClass = new ReflectionClass($class);

                foreach ($constructorParameterNames as $constructorParameterName) {
                    $reflectionClass->getProperty($constructorParameterName)->setRawValue($class, $constructorParameters[$constructorParameterName]);
                }
            } catch (ReflectionException) {
                throw new LazyObjectException('Переданные для создания отложенного объекта свойства не соответствую декларированным в классе типам');
            }
        };
    }

    /**
     * @param non-empty-string $property
     */
    protected function getGroupClosureByPropertyName(string $property): ?LazyGroup
    {
        foreach ($this->lazyGroupProperties as $group) {
            if (! $group->hasProperty($property)) {
                continue;
            }

            return $group;
        }

        return null;
    }
}
