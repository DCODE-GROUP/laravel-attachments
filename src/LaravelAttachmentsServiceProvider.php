<?php

namespace Dcodegroup\LaravelAttachments;

use Closure;
use Illuminate\Support\ServiceProvider;

class LaravelAttachmentsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/attachments.php', 'attachments',
        );
    }

    public function boot()
    {
        $this->setUpConfig();
        $this->setUpRouting();
        $this->setUpTranslations();
        $this->setUpMigrations();
        $this->setUpViews();
    }

    protected function setUpConfig()
    {
        $this->publishes([
            __DIR__.'/../config/attachments.php' => $this->app->configPath('attachments.php'),
        ], 'laravel-attachments-config');
    }

    protected function setUpRouting()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/attachments.php');
    }

    protected function setUpTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'attachments');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/attachments'),
        ], 'laravel-attachments-translations');
    }

    protected function setUpMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => $this->app->databasePath('migrations'),
        ], 'laravel-attachments-migrations');
    }

    protected function setUpViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'attachments');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/attachments'),
        ], 'laravel-attachments-views');
    }
}
