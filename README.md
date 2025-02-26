# neontsun/lazy-object

[![Latest Stable Version](https://poser.pugx.org/neontsun/lazy-object/v?style=for-the-badge)](https://packagist.org/packages/neontsun/lazy-object)
[![PHP Version Require](https://poser.pugx.org/neontsun/lazy-object/require/php?style=for-the-badge)](https://packagist.org/packages/neontsun/lazy-object)
[![License](https://poser.pugx.org/neontsun/lazy-object/license?style=for-the-badge)](https://packagist.org/packages/neontsun/lazy-object)
[![Total Downloads](https://poser.pugx.org/neontsun/lazy-object/downloads?style=for-the-badge)](https://packagist.org/packages/neontsun/lazy-object)
[![Latest Unstable Version](https://poser.pugx.org/neontsun/lazy-object/v/unstable?style=for-the-badge)](https://packagist.org/packages/neontsun/lazy-object)

Wrapper package over native [lazy object](https://www.php.net/manual/en/language.oop5.lazy-objects.php) functionality in PHP

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

```
composer require neontsun/lazy-object
```

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

```
composer require --dev neontsun/lazy-object
```

## Usage

Add attribute to constructor fields that should be lazy loaded.
If a constructor field is not marked with the lazy loading attribute, it will be considered a non-lazy field, the value of which must be passed through the **property** builder method.

```php
final readonly class User
{
    public function __construct(
        private(set) string $id,
        #[Lazy]
        private(set) string $name,
        #[Lazy]
        private(set) int $age,
        #[Lazy]
        private(set) string $birthday,
    ) {}
}
```

There are two ways to create a lazy object - through a factory and through the implementation of the lazy loading interface by a class. In the first option, the phpstorm and static analyzers will not know what type was returned after creation, but this is solved by narrowing the type through **instanceof**, in the second option, the type of the created object will be available.

### With factory

```php
use Neontsun\LazyObject\Attribute\Lazy;

final readonly class Test 
{
    public function __construct(
        private(set) string $uuid,
        #[Lazy]
        private(set) array $data,
    ) {}
}
```

```php
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\DTO\Property;

$factory = new LazyObjectFactory();

$ghost = $factory
    ->ghost(Test::class)
    ->property('uuid', 'uuid')
    ->initializer(static function (Property $data): void {
        sleep(10);
        
        $data->value = [1, 2, 3];
    })
    ->build();

var_dump(new ReflectionClass(Test::class)->isUninitializedLazyObject($ghost));
var_dump($ghost);
```

The code above yields the output below:

```
bool(true)

lazy ghost object(Test)#402 (1) {
    ["uuid"]=>
    string(4) "uuid"
    ["data"]=>
    uninitialized(array)
}
```

### With interface

```php
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\Contract\Builder\LazyGhostBuilderInterface;
use Neontsun\LazyObject\Contract\LazyInterface;
use Neontsun\LazyObject\Contract\LazyObjectFactoryInterface;
use Override;
use ReflectionClass;

final readonly class Test implements LazyInterface
{
    public function __construct(
        private(set) string $uuid,
        #[Lazy]
        private(set) array $data,
    ) {}
    
    #[Override]
    public static function lazy(LazyObjectFactoryInterface $factory): LazyGhostBuilderInterface
    {
        return $factory->ghost(self::class);
    }
    
    #[Override]
    public function isUninitialized(): bool
    {
        return new ReflectionClass(self::class)->isUninitializedLazyObject($this);
    }
}
```

```php
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\DTO\Property;

$factory = new LazyObjectFactory();

$ghost = Test::lazy($factory)
    ->property('uuid', 'uuid')
    ->initializer(static function (Property $data): void {
        sleep(10);
        
        $data->value = [1, 2, 3];
    })
    ->build();

// $ghost is Test class for phpstrom 

var_dump($ghost->isUninitializes());
var_dump($ghost);
```

The code above yields the output below:

```
bool(true)

lazy ghost object(Test)#402 (1) {
    ["uuid"]=>
    string(4) "uuid"
    ["data"]=>
    uninitialized(array)
}
```

## Real life case use

```php
use Neontsun\LazyObject\Attribute\Lazy;
use Neontsun\LazyObject\Contract\LazyInterface;

final readonly class Task 
{
    public function __construct(
        private(set) string $title,
        private(set) string $description,
    ) {}
}

final readonly class TaskCollection implements LazyInterface
{
    /**
     * @param list<Task> $items
     */
    public function __construct(
        #[Lazy]
        private(set) array $items,
    ) {}
    
    #[Override]
    public static function lazy(LazyObjectFactoryInterface $factory): LazyGhostBuilderInterface
    {
        return $factory->ghost(self::class);
    }

    #[Override]
    public function isUninitialized(): bool
    {
        return new ReflectionClass(self::class)->isUninitializedLazyObject($this);
    }
    
    // methods...
}

final readonly class UserAggregate implements LazyInterface
{
    public function __construct(
        private(set) string $id,
        #[Lazy]
        private(set) string $name,
        #[Lazy]
        private(set) int $age,
        #[Lazy]
        private(set) string $createdAt,
        private(set) TaskCollection $tasks,
    ) {}
    
    #[Override]
    public static function lazy(LazyObjectFactoryInterface $factory): LazyGhostBuilderInterface
    {
        return $factory->ghost(self::class);
    }

    #[Override]
    public function isUninitialized(): bool
    {
        return new ReflectionClass(self::class)->isUninitializedLazyObject($this);
    }
    
    // methods...
}
```

```php
use Neontsun\LazyObject\LazyObjectFactory;
use Neontsun\LazyObject\DTO\Property;

$factory = new LazyObjectFactory();

$tasksCollection = TaskCollection::lazy($factory)
    ->initializer(static function (Property $items): void {
        $items->value = [
            new Task(
                title: 'Title',
                description: 'Description',
            ),
            // ...
        ];
    })
    ->build();

$userAggregate = UserAggregate::lazy($factory)
    ->property('id', 'uuid')
    ->property('tasks', $tasksCollection)
    ->initializer(static function (Property $name, Property $age, Property $createdAt): void {
        $name->value = 'Name';
        $age->value = 25;
        $createdAt->value = '2025-01-01 12:00:00';
    })
    ->build();

var_dump($userAggregate);
var_dump($userAggregate->isUninitialized());
var_dump($userAggregate->name);
var_dump($userAggregate);
var_dump($userAggregate->isUninitialized());
var_dump($userAggregate->tasks);
var_dump($userAggregate->tasks->isUninitialized());
var_dump($userAggregate->tasks->items);
var_dump($userAggregate->tasks);
var_dump($userAggregate->tasks->isUninitialized());
```

The code above yields the output below:

```
lazy ghost object(UserAggregate)#383 (2) {
  ["id"]=>
  string(4) "uuid"
  ["name"]=>
  uninitialized(string)
  ["age"]=>
  uninitialized(int)
  ["createdAt"]=>
  uninitialized(string)
  ["tasks"]=>
  lazy ghost object(TaskCollection)#392 (0) {
    ["items"]=>
    uninitialized(array)
  }
}

bool(true)

string(4) "Name"

object(UserAggregate)#383 (5) {
  ["id"]=>
  string(4) "uuid"
  ["name"]=>
  string(4) "Name"
  ["age"]=>
  int(25)
  ["createdAt"]=>
  string(19) "2025-01-01 12:00:00"
  ["tasks"]=>
  lazy ghost object(TaskCollection)#392 (0) {
    ["items"]=>
    uninitialized(array)
  }
}

bool(false)

lazy ghost object(TaskCollection)#392 (0) {
  ["items"]=>
  uninitialized(array)
}

bool(true)

array(1) {
  [0]=>
  object(Task)#423 (2) {
    ["title"]=>
    string(5) "Title"
    ["description"]=>
    string(11) "Description"
  }
}

object(TaskCollection)#392 (1) {
  ["items"]=>
  array(1) {
    [0]=>
    object(Task)#423 (2) {
      ["title"]=>
      string(5) "Title"
      ["description"]=>
      string(11) "Description"
    }
  }
}

bool(false)
```
