<?php

namespace Juzaweb\Plugin\Support;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Translation\Translator;
use Juzaweb\Plugin\Contracts\ActivatorInterface;
use Illuminate\Support\Facades\Artisan;
use Juzaweb\Plugin\Json;

abstract class Plugin
{
    use Macroable;

    /**
     * The laravel|lumen application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The plugin name.
     *
     * @var
     */
    protected $name;

    /**
     * The plugin path.
     *
     * @var string
     */
    protected $path;

    /**
     * @var array of cached Json objects, keyed by filename
     */
    protected $moduleJson = [];
    /**
     * @var CacheManager
     */
    private $cache;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var ActivatorInterface
     */
    private $activator;

    /**
     * @var \Illuminate\Routing\Router $router
     */
    private $router;

    /**
     * The constructor.
     * @param Container $app
     * @param $name
     * @param $path
     */
    public function __construct(Container $app, string $name, $path)
    {
        $this->name = $name;
        $this->path = $path;
        $this->cache = $app['cache'];
        $this->files = $app['files'];
        $this->router = $app['router'];
        $this->translator = $app['translator'];
        $this->activator = $app[ActivatorInterface::class];
        $this->app = $app;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName(): string
    {
        $name = explode('/', $this->name);
        $author = Str::studly($name[0]);
        $module = Str::studly($name[1]);
        return $author .'/'. $module;
    }

    /**
     * Get name in snake case.
     *
     * @return string
     */
    public function getSnakeName(): string
    {
        return namespace_snakename($this->name);
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->get('description');
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->get('alias');
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->get('priority');
    }

    /**
     * Get plugin requirements.
     *
     * @return array
     */
    public function getRequires(): array
    {
        return $this->get('require', []);
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path): Plugin
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        if ($this->isLoadFilesOnBoot()) {
            $this->registerFiles();
        }

        $this->fireEvent('boot');
    }

    /**
     * Get json contents from the cache, setting as needed.
     *
     * @param string $file
     *
     * @return Json
     */
    public function json($file = null) : Json
    {
        if ($file === null) {
            $file = 'composer.json';
        }

        return Arr::get($this->moduleJson, $file, function () use ($file) {
            return $this->moduleJson[$file] = new Json($this->getPath() . '/' . $file, $this->files);
        });
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * Get a specific data from composer.json file by given the key.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function getComposerAttr($key, $default = null)
    {
        return $this->json('composer.json')->get($key, $default);
    }

    /**
     * Register the plugin.
     */
    public function register(): void
    {
        $this->registerAliases();

        $this->registerProviders();

        if ($this->isLoadFilesOnBoot() === false) {
            $this->registerFiles();
        }

        $this->registerRoute();

        $this->fireEvent('register');
    }

    /**
     * Register the plugin event.
     *
     * @param string $event
     */
    protected function fireEvent($event): void
    {
        $this->app['events']->dispatch(sprintf('plugin.%s.' . $event, $this->getLowerName()), [$this]);
    }
    /**
     * Register the aliases from this plugin.
     */
    abstract public function registerAliases(): void;

    /**
     * Register the service providers from this plugin.
     */
    abstract public function registerProviders(): void;

    /**
     * Get the path to the cached *_module.php file.
     *
     * @return string
     */
    abstract public function getCachedServicesPath(): string;

    protected function registerRoute()
    {
        $namespace = $this->getNamespace() . 'Http\Controllers';

        $this->router->middleware('admin')
            ->namespace($namespace)
            ->prefix(config('juzaweb.admin_prefix'))
            ->group($this->path . '/src/routes/admin.php');

        $this->router->middleware('api')
            ->namespace($namespace)
            ->prefix('api')
            ->group($this->path . '/src/routes/api.php');
    }

    /**
     * Register the files from this plugin.
     */
    protected function registerFiles(): void
    {
        $files = Arr::get($this->get('autoload', []), 'files', []);
        foreach ($files as $file) {
            include $this->path . '/' . $file;
        }
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }

    /**
     * Determine whether the given status same with the current plugin status.
     *
     * @param bool $status
     *
     * @return bool
     */
    public function isStatus(bool $status) : bool
    {
        return $this->activator->hasStatus($this, $status);
    }

    /**
     * Determine whether the current plugin activated.
     *
     * @return bool
     */
    public function isEnabled() : bool
    {
        return $this->activator->hasStatus($this, true);
    }

    /**
     *  Determine whether the current plugin not disabled.
     *
     * @return bool
     */
    public function isDisabled() : bool
    {
        return !$this->isEnabled();
    }

    /**
     * Set active state for current plugin.
     *
     * @param bool $active
     *
     * @return bool
     */
    public function setActive(bool $active): bool
    {
        return $this->activator->setActive($this, $active);
    }

    /**
     * Disable the current plugin.
     */
    public function disable(): void
    {
        $this->fireEvent('disabling');

        $this->activator->disable($this);
        $this->flushCache();

        $this->fireEvent('disabled');
    }

    /**
     * Enable the current plugin.
     */
    public function enable(): void
    {
        $this->fireEvent('enabling');

        $this->activator->enable($this);
        $this->runMigrate();
        $this->flushCache();

        $this->fireEvent('enabled');
    }

    /**
     * Delete the current plugin.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $this->activator->delete($this);

        return $this->json()->getFilesystem()->deleteDirectory($this->getPath());
    }

    /**
     * Get extra path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getExtraPath(string $path) : string
    {
        return $this->getPath() . '/' . $path;
    }

    /**
     * Check if can load files of plugin on boot method.
     *
     * @return bool
     */
    protected function isLoadFilesOnBoot(): bool
    {
        return config('plugin.register.files', 'register') === 'boot' &&
            // force register method if option == boot && app is AsgardCms
            !class_exists('\Modules\Core\Foundation\AsgardCms');
    }

    private function flushCache(): void
    {
        if (config('plugin.cache.enabled')) {
            $this->cache->store()->flush();
        }
    }

    /**
     * Register a translation file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    private function loadTranslationsFrom(string $path, string $namespace): void
    {
        $this->translator->addNamespace($namespace, $path);
    }

    public function getExtraLarevel($key): array
    {
        $extra = $this->get('extra', []);
        if ($laravel = Arr::get($extra, 'laravel', [])) {
            return Arr::get($laravel, $key, []);
        }

        return [];
    }

    public function getExtraJuzaweb($key, $default = null)
    {
        $extra = $this->get('extra', []);
        if ($laravel = Arr::get($extra, 'juzaweb', [])) {
            return Arr::get($laravel, $key, $default);
        }

        return $default;
    }

    public function getDisplayName()
    {
        $default = ucwords(str_replace('/', ' ', $this->getName()));
        return $this->getExtraJuzaweb('name') ?? $default;
    }

    public function getPluginPath($plugin)
    {
        return $this->path . '/' . $plugin;
    }

    protected function getNamespace()
    {
        $namespace = Arr::get($this->get('autoload', []), 'psr-4', null);
        $namespace = array_keys($namespace)[0];
        return $namespace;
    }

    private function runMigrate()
    {
        Artisan::call('migrate', ['--force'=> true]);
    }
}
