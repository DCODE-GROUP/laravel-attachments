# Laravel Attachments

## Install

```bash
composer require dcodegroup/laravel-attachments
```

Then run

```bash
php artisan vendor:publish --provider="Dcodegroup\LaravelAttachments\LaravelAttachmentsServiceProvider"
```

## Routes 

Add the Routes to the file you need.

```php
Route::attachments();
Route::attachmentAnnotations();
Route::attachmentCategories();
```

## Frontend

Add the following file to to your css file.

```css
 @import "../../vendor/dcodegroup/laravel-attachments/resources/postcss/attachments.pcss";
```

Add the below to the `app.js` file.

```js
import attachmentPlugin from "../../vendor/dcodegroup/laravel-attachments/resources/js/plugin"

app.use(attachmentPlugin)
```

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
    
        'route_name_prefix' => env('LARAVEL_ATTACHMENTS_ROUTE_NAME_PREFIX', 'attachments'),
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

