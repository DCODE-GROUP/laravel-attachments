<?php

namespace Dcodegroup\LaravelAttachments;

use Closure;
use Illuminate\Support\ServiceProvider;

class LaravelAttachmentsServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->setUpMigrations();
    }

    protected function setUpMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-attachments-migrations');
    }
}
