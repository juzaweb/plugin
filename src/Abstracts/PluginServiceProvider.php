<?php

namespace Juzaweb\Plugin\Abstracts;

use Illuminate\Support\ServiceProvider;
use Juzaweb\Plugin\Contracts\RepositoryInterface;
use Juzaweb\Plugin\Providers\AutoloadServiceProvider;
use Juzaweb\Plugin\Providers\BootstrapServiceProvider;
use Juzaweb\Plugin\Providers\ConsoleServiceProvider;
use Juzaweb\Plugin\Providers\ContractsServiceProvider;

abstract class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register all plugins.
     */
    protected function registerModules()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'jw_plugin');

        $this->publishes([
            __DIR__ . '/../views' => resource_path('vendor/juzaweb/plugin/views'),
        ], 'jw_plugin_views');

        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../../config/plugin.php';
        $this->mergeConfigFrom($configPath, 'plugin');

        $this->publishes([
            $configPath => config_path('plugin.php'),
        ], 'jw_plugin');
    }

    /**
     * Register the service provider.
     */
    abstract protected function registerServices();

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [RepositoryInterface::class, 'modules'];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ContractsServiceProvider::class);
        $this->app->register(AutoloadServiceProvider::class);
    }
}
