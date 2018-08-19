<?php
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
