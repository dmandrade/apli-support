<?php

namespace Apli\Support\Traits;

/**
 * Trait Immutable
 * @package Apli\Support\Traits
 */
trait Immutable
{
    /**
     * @param callable|null $callback
     * @return Immutable
     */
    protected function cloneInstance(callable $callback = null)
    {
        $new = clone $this;

        if ($callback === null) {
            return $new;
        }

        $callback($new);

        return $new;
    }
}
