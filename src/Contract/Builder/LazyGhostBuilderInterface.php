<?php

declare(strict_types = 1);

namespace Neontsun\LazyObject\Contract\Builder;

use Closure;
use Neontsun\LazyObject\Exception\LazyObjectException;
use ReflectionException;

/**
 * @template T of object
 */
interface LazyGhostBuilderInterface
{
    /**
     * Помечает поле как отложенное, заполняя его данными, которые
     * возвращает замыкание. Данные будут загружены при обращении к полю.
     *
     * @param non-empty-string $property
     * @param Closure():mixed $lazy
     * @return self<T>
     * @throws LazyObjectException
     */
    public function lazyProperty(string $property, Closure $lazy): self;

    /**
     * Помечает группу полей как отложенные, заполняя их данными, которые
     * возвращает замыкание. ВСЕ данные будут загружены при обращении к ЛЮБОМУ полю.
     *
     * @param non-empty-list<non-empty-string> $properties
     * @param Closure():mixed $lazy
     * @return self<T>
     * @throws LazyObjectException
     */
    public function lazyGroupProperties(array $properties, Closure $lazy): self;

    /**
     * Никак не помечает поле, а просто заполняет его переданными данными.
     * При обращении к полю НЕ инициализирует другие поля.
     *
     * @param non-empty-string $property
     * @return self<T>
     */
    public function property(string $property, mixed $value): self;

    /**
     * @return T
     * @throws ReflectionException
     * @throws LazyObjectException
     */
    public function build(): object;
}
