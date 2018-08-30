<?php
/**
 *  Copyright (c) 2018 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 *  @project apli
 *  @file Jsonable.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 27/08/18 at 10:27
 */

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
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0);
}
