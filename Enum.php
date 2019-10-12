<?php
/**
 *  Copyright (c) 2019 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 *  @project apli
 *  @file Enum.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 12/10/19 at 20:03
 */

namespace Apli\Support;

use Apli\Support\Traits\Macroable;
use BadMethodCallException;
use ReflectionClass;
use ReflectionException;
use UnexpectedValueException;

/**
 * Class Enum
 * @package Apli\Support
 */
abstract class Enum
{
    use Macroable {
        __callStatic as macroCallStatic;
    }

    /**
     * The key of one of the enum members.
     *
     * @var mixed
     */
    public $key;
    /**
     * The value of one of the enum members.
     *
     * @var mixed
     */
    public $value;
    /**
     * The description of one of the enum members.
     *
     * @var mixed
     */
    public $description;

    /**
     * Constants cache.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Creates a new value of some type.
     *
     * @param mixed|null $value  Initial value
     * @param bool       $strict Provided for SplEnum compatibility
     *
     * @throws UnexpectedValueException if incompatible type is given.
     * @throws ReflectionException
     */
    public function __construct($value = null, $strict = false)
    {
        if (!static::hasValue($value, $strict)) {
            throw new InvalidEnumMemberException($value, $this);
        }
        $this->value = $value;
        $this->key = static::getKey($value);
        $this->description = static::getDescription($value);
    }


    /**
     * Attempt to instantiate an enum by calling the enum key as a static method.
     *
     * This function defers to the macroable __callStatic function if a macro is found using the static method called.
     *
     * @param $method
     * @param $parameters
     * @return Enum|mixed
     * @throws ReflectionException
     */
    public static function __callStatic($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return static::macroCallStatic($method, $parameters);
        }
        if (static::hasKey($method)) {
            $value = static::getValue($method);
            return new static($value);
        }
        throw new BadMethodCallException("Cannot create an enum instance for $method. The enum value $method does not exist.");
    }

    /**
     * Return the enum value (value of the constant)
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Checks if this instance is equal to the given enum instance.
     *
     * @param  Enum  $enum
     * @return bool
     */
    public function equal(Enum $enum): bool
    {
        if ($enum instanceof static) {
            return $this->value === $enum->value;
        }
        return false;
    }

    /**
     * Checks if this instance is not equal to the given enum instance.
     *
     * @param  Enum $enum
     * @return bool
     */
    public function isNotEqual(Enum $enum): bool
    {
        return ! $this->equal($enum);
    }

    /**
     * Checks if a matching enum instance is in the given array.
     *
     * @param array $values
     * @return bool
     */
    public function in(array $enums): bool
    {
        foreach ($enums as $enum) {
            if ($this->equal($enum)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return a new Enum instance
     *
     * @param $value
     * @return static
     * @throws ReflectionException
     */
    public static function getInstance($value): self
    {
        if ($value instanceof static) {
            return $value;
        }
        return new static($value);
    }

    /**
     * Return instances of all the contained values.
     *
     * @return array|static[]
     * @throws ReflectionException
     */
    public static function getInstances(): array
    {
        return array_map(
            static function ($constantValue) {
                return new static($constantValue);
            },
            static::getConstants()
        );
    }

    /**
     * Attempt to instantiate a new Enum using the given value if it exists.
     *
     * @param $value
     * @return Enum|null
     * @throws ReflectionException
     */
    public static function coerce($value): ?self
    {
        return static::hasValue($value) ? static::getInstance($value) : null;
    }

    /**
     * Get all of the constants defined on the class.
     *
     * @return array
     * @throws ReflectionException
     */
    protected static function getConstants(): array
    {
        $calledClass = static::class;
        if (!array_key_exists($calledClass, static::$cache)) {
            $reflect = new ReflectionClass($calledClass);
            static::$cache[$calledClass] = $reflect->getConstants();
        }
        return static::$cache[$calledClass];
    }

    /**
     * Get all of the enum keys.
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getKeys(): array
    {
        return array_keys(static::getConstants());
    }

    /**
     * Get all of the enum values.
     *
     * @return array
     * @throws ReflectionException
     */
    public static function getValues(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * Get the key for a single enum value.
     *
     * @param $key
     * @return string
     * @throws ReflectionException
     */
    public static function getKey($key): string
    {
        return array_search($key, static::getConstants(), true);
    }

    /**
     * Get the value for a single enum key
     *
     * @param  string  $key
     * @return mixed
     * @throws ReflectionException
     */
    public static function getValue(string $key)
    {
        return static::getConstants()[$key];
    }

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     * @throws ReflectionException
     */
    public static function getDescription($value): string
    {
        return static::getFriendlyKeyName(static::getKey($value));
    }

    /**
     * Get a random key from the enum.
     *
     * @return string
     * @throws ReflectionException
     */
    public static function getRandomKey(): string
    {
        $keys = static::getKeys();
        return $keys[array_rand($keys)];
    }

    /**
     * Get a random value from the enum.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public static function getRandomValue()
    {
        $values = static::getValues();
        return $values[array_rand($values)];
    }

    /**
     * Get a random instance of the enum.
     *
     * @return Enum
     * @throws ReflectionException
     */
    public static function getRandomInstance(): self
    {
        return new static(static::getRandomValue());
    }

    /**
     * Return the enum as an array.
     *
     * @return array
     * @throws ReflectionException
     */
    public static function toArray(): array
    {
        return static::getConstants();
    }

    /**
     * Get the enum as an array formatted for a select.
     *
     * @return array
     * @throws ReflectionException
     */
    public static function toSelectArray(): array
    {
        $array = static::toArray();
        $selectArray = [];
        foreach ($array as $value) {
            $selectArray[$value] = static::getDescription($value);
        }
        return $selectArray;
    }

    /**
     * Check that the enum contains a specific key.
     *
     * @param string $key
     * @return bool
     * @throws ReflectionException
     */
    public static function hasKey(string $key): bool
    {
        return in_array($key, static::getKeys(), true);
    }

    /**
     * Check that the enum contains a specific value
     *
     * @param      $value
     * @param bool $strict
     * @return bool
     * @throws ReflectionException
     */
    public static function hasValue($value, bool $strict = true): bool
    {
        $validValues = static::getValues();
        if ($strict) {
            return in_array($value, $validValues, true);
        }
        return in_array((string) $value, array_map('strval', $validValues), true);
    }

    /**
     * Transform the key name into a friendly, formatted version.
     *
     * @param string $key
     * @return string
     */
    protected static function getFriendlyKeyName(string $key): string
    {
        if (ctype_upper(str_replace('_', '', $key))) {
            $key = strtolower($key);
        }
        return ucfirst(str_replace('_', ' ', Str::snake($key)));
    }
}
