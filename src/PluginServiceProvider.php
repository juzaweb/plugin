<?php

namespace Juzaweb\Plugin;

use Juzaweb\Core\Facades\HookAction;
use Juzaweb\Plugin\Contracts\ActivatorInterface;
use Juzaweb\Plugin\Contracts\RepositoryInterface;
use Juzaweb\Plugin\Exceptions\InvalidActivatorClass;
use Juzaweb\Plugin\Laravel\LaravelFileRepository;
use Juzaweb\Plugin\Support\Stub;
use Composer\Autoload\ClassLoader;
use Illuminate\Support\Str;

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

        if (config('plugin.autoload')) {
            $this->pluginAutoload();
        }
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

    protected function pluginAutoload()
    {
        $pluginFile = base_path('bootstrap/cache/plugins_statuses.php');
        if (!file_exists($pluginFile)) {
            return;
        }

        $plugins = require $pluginFile;
        if (empty($plugins)) {
            return;
        }

        $pluginsFolder = config('plugin.paths.modules');
        $loader = new ClassLoader();

        foreach ($plugins as $pluginInfo) {
            foreach ($pluginInfo as $key => $item) {
                $path = $pluginsFolder . '/' . $item['path'];
                $namespace = $item['namespace'] ?? '';

                if (is_dir($path) && $namespace) {
                    $loader->setPsr4($namespace, [$path]);
                    $this->registerPlublish($path, $namespace);
                }
            }
        }

        $loader->register(true);
    }

    protected function registerPlublish($path, $namespace)
    {
        $namespace = str_replace('\\', '/', $namespace);
        $namespace = Str::lower(trim($namespace, '/'));

        $snakeName = Str::snake(preg_replace('/[^0-9a-z]/', '_', $namespace));

        $viewFolder = $path . '/resources/views';
        $langFolder = $path . '/resources/lang';

        if (is_dir($viewFolder)) {
            $viewPublic = resource_path('views/plugins/' . $namespace);
            $this->publishes([
                $viewFolder => $viewPublic,
            ], $snakeName);
        }

        if (is_dir($langFolder)) {
            $langPublic = resource_path('lang/plugins/' . $namespace);
            $this->publishes([
                $langFolder => $langPublic,
            ], $snakeName);
        }
    }
}
