<?php
/**
 *  Copyright (c) 2019 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 * @project apli
 * @file Str.php
 * @author Danilo Andrade <danilo@webbingbrasil.com.br>
 * @date 03/02/19 at 22:29
 */

/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 05/09/2018
 * Time: 11:19.
 */

namespace Apli\Support;

use Apli\Support\Traits\Macroable;
use mb_strtolower;
use mb_strtoupper;
use mb_convert_case;
use ucwords;
use str_replace;
use ctype_lower;
use preg_replace;

class Str
{
    use Macroable;

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];


    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, Constants::UTF8);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, Constants::UTF8);
    }

    /**
     * Convert the given string to title case.
     *
     * @param string $value
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, Constants::UTF8);
    }
}
