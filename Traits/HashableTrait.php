<?php
/**
 * Created by PhpStorm.
 * User: Danilo
 * Date: 19/08/2018
 * Time: 17:18
 */

namespace Apli\Support\Traits;

use Apli\Support\Hashable;

/**
 * Trait HashableTrait
 * @package Apli\Support\Traits
 */
trait HashableTrait
{
    /**
     * Determines if two objects should be considered equal.
     *
     * @param Hashable $other Instance of the same class to compare to.
     * @return bool
     */
    function equals(Hashable $other)
    {
        return $other instanceof $this && $other->hash() == $this->hash();
    }

    /**
     * Produces a scalar value to be used as the object's hash.
     *
     * @return mixed
     */
    function hash()
    {
        return md5(var_export($this, true), false);
    }
}
