<?php

namespace Juzaweb\Plugin\Traits;

trait MigrationLoaderTrait
{
    /**
     * Include all migrations files from the specified plugin.
     *
     * @param string $module
     */
    protected function loadMigrationFiles($module)
    {
        $path = $this->laravel['modules']->getModulePath($module) . $this->getMigrationGeneratorPath();

        $files = $this->laravel['files']->glob($path . '/*_*.php');

        foreach ($files as $file) {
            $this->laravel['files']->requireOnce($file);
        }
    }

    /**
     * Get migration generator path.
     *
     * @return string
     */
    protected function getMigrationGeneratorPath()
    {
        return $this->laravel['modules']->config('paths.generator.migration');
    }
}
