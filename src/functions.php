<?php

if (!function_exists('tinker')) {
    /**
     * Command to return the eval-able code to startup PsySH.
     *
     *     eval(\LaravelFly\tinker());
     *
     * @return string
     */
    function tinker()
    {
        return 'extract(\LaravelFly\Tinker\Shell::debug(get_defined_vars(), $this ?? null));';
    }
}
