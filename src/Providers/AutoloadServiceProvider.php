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
use Juzaweb\Core\Facades\HookAction;

class AutoloadServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootPlugins();
    }

    public function register()
    {
        if (config('plugin.autoload')) {
            $this->autoloadPlugins();
        }
    }

    protected function autoloadPlugins()
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
    }

    protected function registerDatabase($path)
    {
        $this->loadMigrationsFrom($path . '/database/migrations');
        if (!app()->environment('production') && $this->app->runningInConsole()) {
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

    protected function bootPlugins()
    {
        $plugins = $this->getActivePlugins();
        if (empty($plugins)) {
            return;
        }

        $pluginsFolder = $this->getPluginsPath();
        foreach ($plugins as $pluginInfo) {
            foreach ($pluginInfo as $key => $item) {
                $path = $pluginsFolder . '/' . $item['path'];
                $namespace = $item['namespace'] ?? '';
                $snakeName = Str::snake(preg_replace('/[^0-9a-z]/', '_', $namespace));

                if (is_dir($path) && $namespace) {
                    $this->bootActions($path, $namespace, $snakeName);
                }
            }
        }
    }

    protected function bootActions($path, $namespace, $snakeName)
    {
        $actionPath = $path .'/../actions';
        if (is_dir($actionPath)) {
            $this->bootViews($path, $namespace, $snakeName);
            HookAction::loadActionForm($actionPath);
        }
    }

    public function bootViews($path, $namespace, $snakeName)
    {
        $sourcePath = $path .'/resources/views';
        $langPath = $path . '/resources/lang';
        $assetsPath = $path .'/resources/assets';

        if (is_dir($sourcePath)) {
            $this->loadViewsFrom($sourcePath, $snakeName);

            $viewPublic = resource_path('views/vendor/' . $snakeName);
            $this->publishes([
                $sourcePath => $viewPublic,
            ], $snakeName . '_views');
        }

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $snakeName);
            $langPublic = resource_path('lang/vendor/' . $snakeName);

            $this->publishes([
                $langPath => $langPublic,
            ], $snakeName . '_lang');
        }

        if (is_dir($assetsPath)) {
            $assetsPublic = public_path('plugins/' . $namespace . '/assets');
            $this->publishes([
                $assetsPath => $assetsPublic,
            ], $snakeName . '_assets');
        }
    }
}