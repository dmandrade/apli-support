# Enum

Enum support class implementation inspired from SplEnum


## Jump To

* [Declaration](#declaration)
* [Usage](#usage)
* [Type Hinting](#type-hinting)
* [Instance Properties](#instance-properties)
* [Reference](#reference)
    * [Static Methods](#static-methods)
* [IDE Autocompletion](#ide-autocompletion)

### Declaration

```php
use Apli\Support\Enum;

/**
 * State enum
 */
final class State extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const EXPIRED = 'expired';
    const DESTROYED = 'destroyed';
}
```

### Usage

```php
$state = new State(State::ACTIVE);
// or
$state = State::ACTIVE();
// Standard new PHP class, passing the desired enum value as a parameter
$state = new State(State::ACTIVE);

// Static getInstance method, again passing the desired enum value as a parameter
$state = State::getInstance(State::INACTIVE);

// Statically calling the key name as a method, utilizing __callStatic magic
$state = State::INACTIVE();

// Using the coerce static method to attempt to instantiate an Enum using the given value if it exists.
$state = State::coerce($someValue);
```

Static methods are automatically implemented to provide quick access to an enum value.

### Type Hinting

As each constant is a class you have the advantage of type-hint:

```php
function setState(State $state) {
    if($state->equal(State::DESTROYED){
        $this->deleted_at = new \DateTime();
    }
}
```

### Instance Properties

Once you have an enum instance, you can access the `key`, `value` and `description` as properties.

```php
$state = State::getInstance(State::ACTIVE);

$state->key; // ACTIVE
$state->value; // active
$state->description; // Active
```

### Reference

- `__toString(): string` Return the enum value (value of the constant).
- `equal(Enum $enum): bool` Checks if this instance is equal to the given enum instance.
- `isNotEqual(Enum $enum): bool` Checks if this instance is not equal to the given enum instance.
- `in(array $enums): bool` Checks if a matching enum instance is in the given array.

#### Static methods:

- `static getKeys(): array` Returns an array of the keys for an enum.
- `static getValues(): array` Returns an array of the values for an enum.
- `static getKey(mixed $value): string` Returns the key for the given enum value.
- `static getValue(string $key): mixed` Returns the value for the given enum key.
- `static hasKey(string $key): bool` Check if the enum contains a given key.
- `static hasValue(mixed $value, bool $strict = true): bool` Check if the enum contains a given value.
- `static getDescription(mixed $value): string` Returns the key in sentence case for the enum value. It's possible to override the getDescription method to return custom descriptions.
- `static getRandomKey(): string` Returns a random key from the enum. Useful for factories.
- `static getRandomValue(): mixed` Returns a random value from the enum. Useful for factories.
- `static getRandomInstance(): mixed` Returns a random instance of the enum. Useful for factories.
- `static toArray(): array` Returns the enum key value pairs as an associative array.
- `static toSelectArray(): array` Returns the enum for use in a select as value => description.
- `static getInstance(mixed $enumValue): Enum` Returns an instance of the called enum.
- `static getInstances(): array` Returns an array of all possible instances of the called enum, keyed by the constant names.
- `static coerce(): ?Enum` Attempt to instantiate a new Enum using the given value if it exists. Returns null if it doesn't.

### IDE Autocompletion

If you care about IDE autocompletion, you can use phpdoc (this is supported in PhpStorm for example):

```php
/**
 * Class State
 *
 * @method static State ACTIVE()
 * @method static State INACTIVE()
 */
class State extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
}
```
