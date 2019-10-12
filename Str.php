<?php
/**
 *  Copyright (c) 2019 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 *  @project apli
 *  @file Str.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 03/02/19 at 22:29
 */

/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 05/09/2018
 * Time: 11:19.
 */

namespace Apli\Support;

use Apli\Support\Traits\Macroable;

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
     * Convert a value to studly caps case.
     *
     * @param  string  $value
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
}
