<?php

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     * @template TValue
     * @template TReturn
     *
     * @param (Closure():TReturn)|TValue $value
     * @param mixed                      ...$args
     * @return ($value is Closure ? TReturn : TValue)
     */
    function value(mixed $value, ...$args)
    {
        return \Hyperf\Support\value($value, ...$args);
    }
}