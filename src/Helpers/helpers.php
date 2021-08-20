<?php

use Illuminate\Support\Str;

if (! function_exists('plugin_path')) {
    function plugin_path($name, $path = '')
    {
        $module = app('modules')->find($name);

        return $module->getPath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

function namespace_snakename(string $namespace)
{
    return Str::snake(preg_replace('/[^0-9a-z]/', ' ', strtolower($namespace)));
}

