<?php

namespace Dcodegroup\LaravelAttachments;

use Closure;
use Dcodegroup\LaravelAttachments\Http\Controllers\AttachController;
use Dcodegroup\LaravelAttachments\Http\Controllers\DeleteController;
use Dcodegroup\LaravelAttachments\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;
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
        Route::macro('attachmentRoutes', function (
            string $prefix = 'media',
            string $name = 'media',
        ) {
            Route::post("$prefix/attach", AttachController::class)->name("$name.attach");
            Route::post("$prefix/upload", UploadController::class)->name("$name.upload");
            Route::delete("$prefix/delete/{media}", DeleteController::class)->name("$name.delete");
        });
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
