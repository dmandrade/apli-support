<?php
/**
 *  Copyright (c) 2018 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 *  @project apli
 *  @file Arrayable.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 18/08/18 at 16:47
 */

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
