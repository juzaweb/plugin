<?php

namespace Juzaweb\Plugin;

use Juzaweb\Core\Facades\HookAction;
use Juzaweb\Plugin\Contracts\ActivatorInterface;
use Juzaweb\Plugin\Contracts\RepositoryInterface;
use Juzaweb\Plugin\Exceptions\InvalidActivatorClass;
use Juzaweb\Plugin\Laravel\LaravelFileRepository;
use Juzaweb\Plugin\Support\Stub;

class PluginServiceProvider extends ModulesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        HookAction::loadActionForm(__DIR__ . '/../actions');
        $this->registerModules();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerNamespaces();
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        Stub::setBasePath(__DIR__ . '/../stubs');
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(RepositoryInterface::class, function ($app) {
            $path = config('plugin.paths.modules');
            return new LaravelFileRepository($app, $path);
        });

        $this->app->singleton(ActivatorInterface::class, function ($app) {
            $activator = config('plugin.activator');
            $class = config('plugin.activators.' . $activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });

        $this->app->alias(RepositoryInterface::class, 'modules');
    }
}
