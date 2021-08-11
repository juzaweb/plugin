<?php

namespace Juzaweb\Plugin\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\Plugin\Contracts\RepositoryInterface;
use Juzaweb\Plugin\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
