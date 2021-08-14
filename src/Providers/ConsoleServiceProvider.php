<?php

namespace Juzaweb\Plugin\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\Plugin\Commands\CommandMakeCommand;
use Juzaweb\Plugin\Commands\ControllerMakeCommand;
use Juzaweb\Plugin\Commands\DisableCommand;
use Juzaweb\Plugin\Commands\DumpCommand;
use Juzaweb\Plugin\Commands\EnableCommand;
use Juzaweb\Plugin\Commands\EventMakeCommand;
use Juzaweb\Plugin\Commands\FactoryMakeCommand;
use Juzaweb\Plugin\Commands\InstallCommand;
use Juzaweb\Plugin\Commands\JobMakeCommand;
use Juzaweb\Plugin\Commands\LaravelModulesV6Migrator;
use Juzaweb\Plugin\Commands\ListCommand;
use Juzaweb\Plugin\Commands\ListenerMakeCommand;
use Juzaweb\Plugin\Commands\MailMakeCommand;
use Juzaweb\Plugin\Commands\MiddlewareMakeCommand;
use Juzaweb\Plugin\Commands\MigrateCommand;
use Juzaweb\Plugin\Commands\MigrateRefreshCommand;
use Juzaweb\Plugin\Commands\MigrateResetCommand;
use Juzaweb\Plugin\Commands\MigrateRollbackCommand;
use Juzaweb\Plugin\Commands\MigrateStatusCommand;
use Juzaweb\Plugin\Commands\MigrationMakeCommand;
use Juzaweb\Plugin\Commands\ModelMakeCommand;
use Juzaweb\Plugin\Commands\ModuleDeleteCommand;
use Juzaweb\Plugin\Commands\ModuleMakeCommand;
use Juzaweb\Plugin\Commands\NotificationMakeCommand;
use Juzaweb\Plugin\Commands\PolicyMakeCommand;
use Juzaweb\Plugin\Commands\ProviderMakeCommand;
use Juzaweb\Plugin\Commands\RequestMakeCommand;
use Juzaweb\Plugin\Commands\ResourceMakeCommand;
use Juzaweb\Plugin\Commands\RouteProviderMakeCommand;
use Juzaweb\Plugin\Commands\RuleMakeCommand;
use Juzaweb\Plugin\Commands\SeedCommand;
use Juzaweb\Plugin\Commands\SeedMakeCommand;
use Juzaweb\Plugin\Commands\SetupCommand;
use Juzaweb\Plugin\Commands\TestMakeCommand;
use Juzaweb\Plugin\Commands\UnUseCommand;
use Juzaweb\Plugin\Commands\UpdateCommand;
use Juzaweb\Plugin\Commands\UseCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [
        CommandMakeCommand::class,
        ControllerMakeCommand::class,
        DisableCommand::class,
        //DumpCommand::class,
        EnableCommand::class,
        EventMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        //MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        //NotificationMakeCommand::class,
        ProviderMakeCommand::class,
        RouteProviderMakeCommand::class,
        //InstallCommand::class,
        ListCommand::class,
        ModuleDeleteCommand::class,
        ModuleMakeCommand::class,
        //FactoryMakeCommand::class,
        //PolicyMakeCommand::class,
        RequestMakeCommand::class,
        RuleMakeCommand::class,
        MigrateCommand::class,
        MigrateRefreshCommand::class,
        MigrateResetCommand::class,
        MigrateRollbackCommand::class,
        MigrateStatusCommand::class,
        MigrationMakeCommand::class,
        ModelMakeCommand::class,
        SeedCommand::class,
        SeedMakeCommand::class,
        //SetupCommand::class,
        //UnUseCommand::class,
        //UpdateCommand::class,
        //UseCommand::class,
        ResourceMakeCommand::class,
        TestMakeCommand::class,
        LaravelModulesV6Migrator::class,
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * @return array
     */
    public function provides()
    {
        $provides = $this->commands;

        return $provides;
    }
}
