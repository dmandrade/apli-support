<?php
/**
 *  Copyright (c) 2018 Danilo Andrade.
 *
 *  This file is part of the apli project.
 *
 * @project apli
 * @file AbstractEnum.php
 *
 * @author Danilo Andrade <danilo@webbingbrasil.com.br>
 * @date 27/08/18 at 10:26
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
     * List of labels for enums constants.
     *
     * @var array
     */
    public static $labels = [];

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
     * Set if is strict.
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
     * Return the array of labels.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function labels()
    {
        $result = [];
        foreach (static::values() as $value) {
            $result[] = static::getLabel($value);
        }

        return $result;
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
     * Returns all possible values as an array, except default constant.
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    public static function toArray()
    {
        return self::getConstants(false);
    }

    /**
     * Returns all enum constants.
     *
     * @param bool $includeDefault
     *
     * @throws ReflectionException
     *
     * @return array|mixed
     */
    public static function getConstants($includeDefault = true)
    {
        $className = get_called_class();
        if (!array_key_exists($className, static::$cache)) {
            $reflection = new \ReflectionClass($className);
            static::$cache[$className] = $reflection->getConstants();

            if (method_exists($className, 'boot')) {
                static::boot();
            }
        }

        $constants = self::$cache[$className];

        if ($includeDefault === false) {
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
     * Returns the label for a given value.
     *
     * !!Make sure it only gets called after bootClass()!!
     *
     * @param $value
     *
     * @return string
     */
    private static function getLabel($value)
    {
        if (static::hasLabels() && isset(static::$labels[$value])) {
            return (string) static::$labels[$value];
        }

        return (string) $value;
    }

    /**
     * Returns whether the labels property is defined on the actual class.
     *
     * @return bool
     */
    private static function hasLabels()
    {
        return property_exists(static::class, 'labels');
    }

    /**
     * Return a array with value/label pairs.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function choices()
    {
        $result = [];
        foreach (static::values() as $value) {
            $result[$value] = static::getLabel($value);
        }

        return $result;
    }

    /**
     * Check if enum key exists.
     *
     * @param string $name   Name of the constant to validate
     * @param bool   $strict Case is significant when searching for name
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    public static function isValidName($name, $strict = true)
    {
        $constants = static::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $constantNames = array_map('strtoupper', array_keys($constants));

        return in_array(strtoupper($name), $constantNames);
    }

    /**
     * Check if is valid enum value.
     *
     * @param      $value
     * @param bool $strict Case is significant when searching for name
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    public static function isValidValue($value, $strict = true)
    {
        return in_array($value, static::toArray(), $strict);
    }

    /**
     * Provided for compatibility with SplEnum.
     *
     * @see AbstractEnum::getConstants()
     *
     * @param bool $include_default Include `__default` and its value. Not included by default.
     *
     * @throws ReflectionException
     *
     * @return array
     */
    public static function getConstList($include_default = false)
    {
        return self::getConstants($include_default);
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
     * @param       $name
     * @param array $arguments
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    public function __call($name, array $arguments)
    {
        if (strpos($name, 'is') === 0 && strlen($name) > 2 && ctype_upper($name[2])) {
            $constName = self::strToConstName(substr($name, 2));
            if (self::hasConst($constName)) {
                return $this->equalsByConstName($constName);
            }
        }
        trigger_error(
            sprintf('Call to undefined method: %s::%s()', static::class, $name),
            E_USER_WARNING
        );
    }

    /**
     * @param $str
     *
     * @return string
     */
    private static function strToConstName($str)
    {
        if (!ctype_lower($str)) {
            $str = preg_replace('/\s+/u', '', ucwords($str));
            $str = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.'_', $str));
        }

        return strtoupper($str);
    }

    /**
     * Returns whether a const is present in the specific enum class.
     *
     * @param $const
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    public static function hasConst($const)
    {
        return in_array($const, static::keys());
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
     * Returns whether the enum instance equals with a value of the same
     * type created from the given const name.
     *
     * @param $const
     *
     * @throws ReflectionException
     *
     * @return bool
     */
    private function equalsByConstName($const)
    {
        return $this->equals(
            new static(constant(static::class.'::'.$const))
        );
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
     * Set enum value.
     *
     * @param $value
     *
     * @throws ReflectionException
     */
    public function setValue($value)
    {
        $className = get_called_class();

        if (is_null($value)) {
            if (!self::isValidName(self::$defaultKey, $this->_strict)) {
                throw new UnexpectedValueException('Default value not defined in enum '.$className);
            }

            $value = self::getConstants()[self::$defaultKey];
        }

        if (!self::isValidValue($value, $this->_strict)) {
            throw new UnexpectedValueException("Value '$value' is not part of the enum ".get_called_class());
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
        return serialize(['__default' => $this->value]);
    }

    public function unserialize($serialized)
    {
        $this->value = unserialize($serialized)['__default'];
        $this->_strict = false;
    }
}
