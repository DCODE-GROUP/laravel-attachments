<?php

namespace Dcodegroup\LaravelAttachments;

use Dcodegroup\LaravelAttachments\Http\Controllers\Annotations\DeleteController as AnnotationsDeleteController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Annotations\StoreController as AnnotationsStoreController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Categories\CategoriesController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Categories\OptionsController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\AttachController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\DeleteController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\ExistingController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\SetAltTextController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\SetCategoryController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\SetTitleController;
use Dcodegroup\LaravelAttachments\Http\Controllers\Media\UploadController;
use Dcodegroup\LaravelAttachments\Models\Media;
use Dcodegroup\LaravelAttachments\Observer\MediaObserver;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
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

        Media::observe(MediaObserver::class);
    }

    protected function setUpConfig()
    {
        $this->publishes([
            __DIR__.'/../config/attachments.php' => $this->app->configPath('attachments.php'),
        ], 'laravel-attachments-config');
    }

    protected function setUpRouting()
    {
        Route::macro('attachments', function (
            string $prefix = 'media',
            string $name = 'media',
        ) {
            Route::post("$prefix/attach", AttachController::class)->name("$name.attach");
            Route::post("$prefix/upload", UploadController::class)->name("$name.upload");
            Route::get("$prefix/existing", ExistingController::class)->name("$name.existing");
            Route::delete("$prefix/delete/{media}", DeleteController::class)->name("$name.delete");

            Route::patch("$prefix/title/{media}", SetTitleController::class)->name("$name.title");
            Route::patch("$prefix/alttext/{media}", SetAltTextController::class)->name("$name.alttext");
        });

        Route::macro('attachmentAnnotations', function (
            string $prefix = 'media',
            string $name = 'media',
        ) {
            Route::post("$prefix/{media}/annotations", AnnotationsStoreController::class)->name("$name.annotations.store");
            Route::delete("$prefix/{media}/annotations/{annotation}", AnnotationsDeleteController::class)->name("$name.annotations.delete");
        });

        Route::macro('attachmentCategories', function (
            string $prefix = 'media',
            string $categoriesPrefix = 'categories',
            string $name = 'media',
            string $categoriesName = 'categories',
        ) {
            Route::patch("$prefix/category/{media}", SetCategoryController::class)->name("$name.category");

            Route::get("$categoriesPrefix/options", OptionsController::class)->name("$categoriesName.options");
            Route::get("$categoriesPrefix", CategoriesController::class)->name("$categoriesName");
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
        if ($this->app->environment('local')) {
            $timestamp = date('Y_m_d_His', time());
            if (! Schema::hasTable('media')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_media_table.stub.php' => $this->app->databasePath('migrations/'.$timestamp.'_create_media_table.php'),
                ], 'laravel-attachments-migrations');
            }

            if (! Schema::hasTable('media_categories')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_media_categories_table.stub.php' => $this->app->databasePath('migrations/'.$timestamp.'_create_media_categories_table.php'),
                ], 'laravel-attachments-migrations');
            }

            if (! Schema::hasTable('annotations')) {
                $this->publishes([
                    __DIR__.'/../database/migrations/create_media_annotations_table.stub.php' => $this->app->databasePath('migrations/'.$timestamp.'_create_annotations_table.php'),
                ], 'laravel-attachments-migrations');
            }
        }

    }

    protected function setUpViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'attachments');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/attachments'),
        ], 'laravel-attachments-views');
    }
}
