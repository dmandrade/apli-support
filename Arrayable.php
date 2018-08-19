<?php
/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 18/08/2018
 * Time: 16:47
 */

namespace Apli\Support;

/**
 * Interface Arrayable
 * @package Apli\Support
 */
interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}
