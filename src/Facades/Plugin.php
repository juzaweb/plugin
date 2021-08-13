<?php

namespace Juzaweb\Plugin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static Plugin[] all()
 * @method static delete(string $plugin)
 * @method static enable(string $plugin)
 * @method static disable(string $plugin)
 * @method static getPath()
 * @method static getPluginPath(string $plugin)
 * @method get(string $key, $default = null)
 * @method getDisplayName()
 * @method bool isEnabled()
 * @see \Juzaweb\Plugin\Laravel\Module
 * */
class Plugin extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'modules';
    }
}
