# Laravel Attachments

Simple Dropin package to add attachments to your models.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcodegroup/laravel-attachments.svg?style=flat-square)](https://packagist.org/packages/dcodegroup/laravel-attachments)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/dcodegroup/laravel-attachments/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/:vendor_slug/laravel-attachments/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/dcodegroup/laravel-attachments.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/laravel-attachments)

## Install

```bash
composer require dcodegroup/laravel-attachments
```

Then run

```bash
php artisan vendor:publish --provider="Dcodegroup\LaravelAttachments\LaravelAttachmentsServiceProvider"
```

## Database

If you are using either `ULIDS` or `UUIDs` in your tables ensure to update the published migrations for the `media` table.


eg. replace with the appropriate type
```php
    Schema::create('media', function (Blueprint $table) {
    ...
        $table->nullableUuidMorphs('model');
        $table->nullableUuidMorphs('parent_model');
```

or

```php
    Schema::create('media', function (Blueprint $table) {
    ...
        $table->nullableUlidMorphs('model');
        $table->nullableUlidMorphs('parent_model');
```

Then run the migrations

```bash 

## Routes 

Add the Routes to the file you need such as `laravel_attachments.php`

```php
<?php

use Illuminate\Support\Facades\Route;

Route::attachments();
Route::attachmentAnnotations();
Route::attachmentCategories();
```

Then add the following to your `RouteServiceProvider`

```php
    /**
     * Attachments
     */
    Route::middleware([
        'web',
        'auth',
    ])->as(config('attachments.route_name_prefix').'.')->prefix('attachments')->group(base_path('routes/laravel_attachments.php'));
```

## Frontend

Add the following file to to your css file.

```css
 @import "../../vendor/dcodegroup/laravel-attachments/resources/scss/attachments.scss";
```

Add the below to the `app.js` file.

```js
import attachmentPlugin from "../../vendor/dcodegroup/laravel-attachments/resources/js/plugin"

app.use(attachmentPlugin)
```

Ensure to install these npm packages

```bash
npm i @heroicons/vue bytes form-backend-validation vue-image-markup vue-upload-component  
```

## config


Configuration file contains

```php

<?php
return [
    'media' => [
        'conversions' => [
            'thumb' => [
                'width' => 43,
                'height' => 43,
            ],
            'list' => [
                'width' => 43,
                'height' => 43,
            ],
            'grid' => [
                'width' => 160,
                'height' => 192,
            ],
        ],
    ],

    'features' => [
        // annotations
        // categories
    ],
    
    'route_name_prefix' => env('LARAVEL_ATTACHMENTS_ROUTE_NAME_PREFIX', 'laravel_attachments'),
    
    'signed' => env('LARAVEL_ATTACHMENTS_URLS_SIGNED', false),
];

```

Ensure the publish the config file from the Spatie Media Library.

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"
```

Then change the media model used to 

```php
    'media_model' => \Dcodegroup\LaravelAttachments\Models\Media::class,
```

Update the path generator from the default to our customised one.

```php
    'path_generator' => \Dcodegroup\LaravelAttachments\MediaLibrary\MediaPathGenerator::class,
```

You probably want to change the disk for stored files from `media-library` also.

```php
    'disk_name' => env('MEDIA_DISK', 's3'),
```

## Security

You will need to implement a `MediaPolicy` to control access to the media.

Then add to `AuthServiceProvider`


```php
    protected $policies = [
        ...
        Media::class => MediaPolicy::class,
    ];

```

Example policy might be

```php
<?php

namespace App\Policies;

use App\Models\User;
use Dcodegroup\LaravelAttachments\Models\Media;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->internalOnly($user);
    }

    public function view(User $user, Media $media): bool
    {
        return $this->internalOnly($user);
    }

    public function create(User $user): bool
    {
        return $this->internalOnly($user);
    }

    public function update(User $user, Media $media): bool
    {
        return $this->internalOnly($user);
    }

    public function delete(User $user, Media $media): bool
    {
        return $this->internalOnly($user);
    }

    public function restore(User $user, Media $media): bool
    {
        return $this->internalOnly($user);
    }

    public function forceDelete(User $user, Media $media): bool
    {
        return $this->internalOnly($user);
    }
}
```

If you are using `S3` or another hosted service you may need to use signed urls to access the urls.

If yes then update the configuration or the ENV variable to true.

```php
    'use_signed_urls' => env('USE_SIGNED_URLS', true),
```

This package will use `dreamonkey/laravel-cloudfront-url-signer` https://github.com/dreamonkey/laravel-cloudfront-url-signer. See the README for how to configure.

Here is how to generate the ssh keys. Make sure to have a directory `storage/cloudfront-keypairs` 

Source of below is from https://docs.aws.amazon.com/AmazonCloudFront/latest/DeveloperGuide/private-content-trusted-signers.html?icmpid=docs_cf_help_panel#private-content-creating-cloudfront-key-pairs

then 

```bash 
openssl genrsa -out private_key.pem 2048
```

then 

```bash
openssl rsa -pubout -in private_key.pem -out public_key.pem
```

## User model

Add the following contract to the `User` model.

```php  

use Dcodegroup\LaravelAttachments\Contracts\HasMediaUser;

class User extends Authenticatable implements HasMediaUser
{
...

public function getMediaUserName(): string
{
    return $this->name;
}
```

## Usage

Add the template to the edit page you want.

```php
    @if($model->exists)
    <div class="py-8">
        <hr class="border-gray-100">
    </div>
    <div class="mt-8">
        @include('attachments::attachments', ['model' => $model])
    </div>
    @endif

```

