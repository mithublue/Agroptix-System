<?php

use App\Models\Option;

if (!function_exists('option')) {
    /**
     * Get a site option value by name, with optional default.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    function option($name, $default = null)
    {
        return Option::get($name, $default);
    }
}
