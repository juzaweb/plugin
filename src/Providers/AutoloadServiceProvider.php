<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/laravel-cms
 * @author     The Anh Dang <dangtheanh16@gmail.com>
 * @link       https://juzaweb.com/cms
 * @license    MIT
 *
 * Created by JUZAWEB.
 * Date: 8/14/2021
 * Time: 5:13 PM
 */

namespace Juzaweb\Plugin\Providers;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class AutoloadServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (config('plugin.autoload')) {
            $this->pluginAutoload();
        }
    }

    protected function pluginAutoload()
    {
        $plugins = $this->getActivePlugins();
        if (empty($plugins)) {
            return;
        }

        $pluginsFolder = $this->getPluginsPath();
        $loader = new ClassLoader();

        foreach ($plugins as $pluginInfo) {
            foreach ($pluginInfo as $key => $item) {
                $path = $pluginsFolder . '/' . $item['path'];
                $namespace = $item['namespace'] ?? '';

                if (is_dir($path) && $namespace) {
                    $loader->setPsr4($namespace, [$path]);
                    $this->registerPlugin($path, $namespace);
                }
            }
        }

        $loader->register(true);
    }

    protected function registerPlugin($path, $namespace)
    {
        $namespace = str_replace('\\', '/', $namespace);
        $namespace = Str::lower(trim($namespace, '/'));
        $snakeName = Str::snake(preg_replace('/[^0-9a-z]/', '_', $namespace));

        $this->registerDatabase($path);
        $this->registerViews($path, $namespace, $snakeName);
        $this->registerTranslation($path, $namespace, $snakeName);
    }

    public function registerViews($path, $namespace, $snakeName)
    {
        $sourcePath = $path .'/resources/views';

        if (is_dir($sourcePath)) {
            $this->loadViewsFrom($sourcePath, $snakeName);

            $viewPublic = resource_path('views/plugins/' . $namespace);
            $this->publishes([
                $sourcePath => $viewPublic,
            ], $snakeName);
        }
    }

    protected function registerTranslation($path, $namespace, $snakeName)
    {
        $langPath = $path . '/resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $snakeName);
            $langPublic = resource_path('lang/plugins/' . $namespace);

            $this->publishes([
                $langPath => $langPublic,
            ], $snakeName);
        }
    }

    protected function registerDatabase($path)
    {
        $this->loadMigrationsFrom($path . '/database/migrations');
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load($path . '/database/factories');
        }
    }

    protected function getActivePlugins()
    {
        $pluginFile = base_path('bootstrap/cache/plugins_statuses.php');
        if (!file_exists($pluginFile)) {
            return false;
        }

        $plugins = require $pluginFile;
        return $plugins;
    }

    protected function getPluginsPath()
    {
        return config('plugin.paths.modules');
    }
}