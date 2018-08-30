<?php
/**
 *  Copyright (c) 2018 Danilo Andrade
 *
 *  This file is part of the apli project.
 *
 *  @project apli
 *  @file Hashable.php
 *  @author Danilo Andrade <danilo@webbingbrasil.com.br>
 *  @date 27/08/18 at 10:27
 */

namespace Apli\Support;

/**
 * Interface Hashable
 * @package Apli\Support
 */
interface Hashable
{
    /**
     * Determines if two objects should be considered equal.
     *
     * @param Hashable $other Instance of the same class to compare to.
     * @return bool
     */
    function equals(Hashable $other);

    /**
     * Produces a scalar value to be used as the object's hash.
     *
     * @return mixed
     */
    function hash();
}
