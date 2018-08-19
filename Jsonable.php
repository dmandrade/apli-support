<?php
/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 18/08/2018
 * Time: 16:47
 */

namespace Apli\Support;

/**
 * Interface Jsonable
 * @package Apli\Support
 */
interface Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);
}
