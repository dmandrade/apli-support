<?php
/**
 *  Copyright (c) 2018 Danilo Andrade (http://daniloandrade.net).
 *
 *  This file is part of the Aplí Framework.
 *
 * @project Aplí Framework
 * @file Environment.php
 *
 * @author Danilo Andrade <danilo@daniloandrade.net>
 * @date 07/07/18 at 17:10
 *
 * @copyright  Copyright (c) 2018 Danilo Andrade
 * @license    GNU Lesser General Public License version 3 or later.
 */

namespace Apli\Support;

use ReflectionException;
use UnexpectedValueException;

/**
 * Enum implementation inspired from SplEnum.
 */
abstract class AbstractEnum implements \JsonSerializable, \Serializable
{
    /**
     * Store existing constants in a static cache per object.
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * Set name of default constant key.
     *
     * @var string
     */
    protected static $defaultKey = '__default';

    /**
     * Set if is strict
     *
     * @var bool
     */
    protected $_strict;

    /**
     * Enum value.
     *
     * @var mixed
     */
    protected $value;

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
        $this->_strict = (bool) $strict;
        $this->setValue($value);
    }

    /**
     * Returns all possible values as an array, except default constant.
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    public static function toArray()
    {
        return self::getConstList(false);
    }

    /**
     * Returns the names (keys) of all constants in the Enum class.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function keys()
    {
        return array_keys(static::toArray());
    }

    /**
     * Returns instances of the Enum class of all Enum constants.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function values()
    {
        $values = [];
        foreach (static::toArray() as $key => $value) {
            $values[$key] = new static($value);
        }

        return $values;
    }

    /**
     * Get a list of constants
     *
     * @param bool $include_default Include `__default` and its value. Not included by default.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function getConstList($include_default = false)
    {
        $className = get_called_class();
        if (!array_key_exists($className, static::$cache)) {
            $reflection = new \ReflectionClass($className);
            static::$cache[$className] = $reflection->getConstants();
        }

        $constants = self::$cache[$className];

        if ($include_default === false) {
            $constants = array_filter(
                $constants,
                function ($key) {
                    return $key !== self::$defaultKey;
                },
                ARRAY_FILTER_USE_KEY);
        }

        return $constants;
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant.
     *
     * @param $name
     * @param $arguments
     *
     * @throws ReflectionException
     * @throws \BadMethodCallException if enum does not exist
     *
     * @return AbstractEnum
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($array[$name]);
        }

        throw new \BadMethodCallException("No static method or enum constant '$name' in class ".get_called_class());
    }

    /**
     * Returns the enum key.
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    public function getKey()
    {
        return static::search($this->value);
    }

    /**
     * Return key for value.
     *
     * @param $value
     *
     * @throws ReflectionException
     *
     * @return false|int|string
     */
    public static function search($value)
    {
        return array_search($value, static::toArray(), true);
    }

    /**
     * Return string representation of the enum's value.
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->value);
    }

    /**
     * Compares one Enum with another.
     *
     * @param AbstractEnum|null $enum
     *
     * @return bool
     */
    final public function equals(self $enum = null)
    {
        return $enum !== null && $this->getValue() === $enum->getValue() && get_called_class() == get_class($enum);
    }

    /**
     * Get enum value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set enum value
     *
     * @param $value
     * @throws ReflectionException
     */
    public function setValue($value)
    {
        $className = get_called_class();
        $constants = static::getConstList();

        if (is_null($value)) {
            if (!isset($constants[self::$defaultKey])) {
                throw new UnexpectedValueException('Default value not defined in enum '.$className);
            }

            $value = $constants[self::$defaultKey];
        }

        if (!in_array($value, $constants, $this->_strict)) {
            throw new UnexpectedValueException("Value '$value' is not part of the enum ".$className);
        }

        $this->value = $value;
    }

    /**
     * Specify data which should be serialized to JSON. This method returns data that can be serialized by json_encode()
     * natively.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }

    public function serialize()
    {
        return serialize(array('__default' => $this->value));
    }
    public function unserialize($serialized)
    {
        $this->value = unserialize($serialized)['__default'];
        $this->_strict = false;
    }
}
