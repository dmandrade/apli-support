<?php
/**
 *  Copyright (c) 2018 Danilo Andrade.
 *
 *  This file is part of the apli project.
 *
 * @project apli
 * @file Arr.php
 *
 * @author Danilo Andrade <danilo@webbingbrasil.com.br>
 * @date 05/09/18 at 11:19
 */

/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 05/09/2018
 * Time: 11:19.
 */

namespace Apli\Support;

use Apli\Support\Traits\Macroable;
use ArrayAccess;
use Closure;
use InvalidArgumentException;
use function array_filter;
use function array_flip;
use function array_intersect_key;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function array_rand;
use function array_reverse;
use function array_shift;
use function array_unshift;
use function array_values;
use function count;
use function end;
use function explode;
use function is_array;
use function is_object;
use function is_string;
use function ksort;
use function method_exists;
use function mt_srand;
use function random_int;
use function shuffle;
use function sort;
use function strpos;
use function usort;

class Arr
{
    use Macroable;

    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function add(array $array, $key, $value): array
    {
        if (static::get($array, $key) === null) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param \ArrayAccess|array $array
     * @param string             $key
     * @param mixed              $default
     *
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return $default;
        }

        if ($key !== null) {
            if (strpos($key, '.') === false || static::exists($array, $key)) {
                return $array[$key] ?? $default;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($array) && static::exists($array, $segment)) {
                    $array = $array[$segment];
                    continue;
                }

                $array = $default;
                break;
            }
        }

        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param \ArrayAccess|array $array
     * @param string|int         $key
     *
     * @return bool
     */
    public static function exists(array $array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public static function set(array &$array, $key, $value): array
    {
        if ($key === null) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function collapse(array $array): array
    {
        return array_merge([], ...$array);
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param array $array
     *
     * @return array
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function except(array $array, $keys): array
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return void
     */
    public static function forget(array &$array, $keys): void
    {
        $original = &$array;

        $keys = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     *
     * @return mixed
     */
    public static function last(array $array, callable $callback = null, $default = null): array
    {
        if ($callback === null) {
            return empty($array) ? $default : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param array         $array
     * @param callable|null $callback
     * @param mixed         $default
     *
     * @return mixed
     */
    public static function first(array $array, callable $callback = null, $default = null): array
    {
        if ($callback instanceof Closure) {
            foreach ($array as $key => $value) {
                if ($callback($value, $key)) {
                    return $value;
                }
            }
        }

        if (!empty($array)) {
            return self::first($array);
        }

        return $default;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param \ArrayAccess|array $array
     * @param string|array       $keys
     *
     * @return bool
     */
    public static function has(array $array, $keys): bool
    {
        $keys = (array)$keys;
        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (!static::accessible($subKeyArray) || !static::exists($subKeyArray, $segment)) {
                    return false;
                }

                $subKeyArray = $subKeyArray[$segment];
            }
        }

        return true;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param array             $array
     * @param string|array      $value
     * @param string|array|null $key
     *
     * @return array
     */
    public static function pluck(array $array, $value, $key = null): array
    {
        $results = [];

        [$value, $key] = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = self::get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if ($key === null) {
                $results[] = $itemValue;
                continue;
            }

            $itemKey = self::get($item, $key);

            if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                $itemKey = (string)$itemKey;
            }

            $results[$itemKey] = $itemValue;

        }

        return $results;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param string|array      $value
     * @param string|array|null $key
     *
     * @return array
     */
    protected static function explodePluckParameters($value, $key): array
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = $key === null || is_array($key) ? $key : explode('.', $key);

        return [$value, $key];
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     *
     * @return array
     */
    public static function prepend($array, $value, $key = null): array
    {
        if ($key !== null) {
            return [$key => $value] + $array;
        }

        array_unshift($array, $value);
        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @param array    $array
     * @param int|null $number
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public static function random(array $array, $number = null)
    {
        $requested = $number ?? 1;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if ($number === null) {
            return $array[array_rand($array)];
        }

        if ((int)$number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array)$keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param array    $array
     * @param int|null $seed
     *
     * @return array
     */
    public static function shuffle(array $array, $seed = null): array
    {
        if ($seed !== null) {
            mt_srand($seed);

            usort($array, function () {
                return random_int(-1, 1);
            });
            return $array;
        }

        shuffle($array);
        return $array;
    }


    /**
     * Recursively sort an array by keys and values.
     *
     * @param array $array
     *
     * @return array
     */
    public static function sortRecursive(array $array): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }
        unset($value);

        if (static::isAssoc($array)) {
            ksort($array);
            return $array;
        }

        sort($array);
        return $array;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param array    $array
     * @param callable $callback
     *
     * @return array
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param mixed $value
     *
     * @return array
     */
    public static function wrap($value): array
    {
        if ($value === null) {
            return [];
        }

        return !is_array($value) ? [$value] : $value;
    }
}
