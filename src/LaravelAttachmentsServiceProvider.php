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
        $this->setUpMigrations();
    }

    protected function setUpConfig()
    {
        $this->publishes([
            __DIR__.'/../config/attachments.php' => config_path('attachments.php'),
        ]);
    }

    protected function setUpMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-attachments-migrations');
    }
}
