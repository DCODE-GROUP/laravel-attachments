# Laravel Attachments

## Install

```bash
composer require dcodegroup/laravel-attachments
```

Then run

```bash
php artisan vendor:publish --provider="DcodeGroup\LaravelAttachments\Providers\AttachmentsServiceProvider"
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
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

Then change the media model used to 

```php
    'media_model' => \Dcodegroup\LaravelAttachments\Models\Media::class,
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

