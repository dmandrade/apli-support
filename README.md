[![StyleCI](https://github.styleci.io/repos/140114775/shield?branch=master)](https://github.styleci.io/repos/140114775)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/02b5b36f35cd4fa18b0e0d292a3f4f65)](https://www.codacy.com/app/mandrade.danilo/apli-support?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=dmandrade/apli-support&amp;utm_campaign=Badge_Grade)

# Aplí Support
       
Aplí Support package provides some core useful tool set.

## Installation via Composer

Add this to the require block in your `composer.json`.

``` json
{
    "require": {
        "apli/support": "~1.0"
    }
}
```

## Enum

Enum support class implementation inspired from SplEnum

### Declaration

```php
use Apli\Support\AbstractEnum;

/**
 * State enum
 */
class State extends AbstractEnum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const EXPIRED = 'expired';
    const DESTROYED = 'destroyed';
    const ERROR = 'error';
}
```

You can declare the enum class using `final` or set the constant to `private` (only supported in PHP>7.1)

### Usage

```php
$state = new State(State::ACTIVE);
// or
$state = State::ACTIVE();
```

Static methods are automatically implemented to provide quick access to an enum value.

As each constant is a class you have the advantage of type-hint:

```php
function setState(State $state) {
    // ...
}
```

### Complete list of methods

- `__toString()` You can `echo $state`, it will display the enum value (value of the constant)
- `getValue()` Returns the current value of the enum
- `getKey()` Returns the key of the current value on Enum
- `equals()` Tests whether enum instances are equal to another

Static methods:

- `toArray()` returns all possible values as an array
- `keys()` Returns the names (keys) of all constants in the Enum class
- `values()` Returns instances of the Enum class of all Enum constants (constant name in key, Enum instance in value)
- `isValidValue()` Check if tested value exists on enum set
- `isValidName()` Check if tested key exists on enum set
- `search()` Return key for searched value

### Static methods

Static method helpers are implemented using [`__callStatic()`](http://www.php.net/manual/en/language.oop5.overloading.php#object.callstatic).

If you care about IDE autocompletion, you can use phpdoc (this is supported in PhpStorm for example):

```php
/**
 * Class State
 *
 * @method static State ACTIVE()
 * @method static State INACTIVE()
 */
class State extends AbstractEnum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
}
```
