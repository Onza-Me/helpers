# OnzaMe/Helper package

## Sentry initialization

    php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
    
### Configure Sentry
    
    SENTRY_LARAVEL_DSN={DSN URL}

You can easily verify that Sentry is capturing errors in your Laravel application by creating a debug route that will throw an exception:

    Route::get('/debug-sentry', function () {
        throw new Exception('My first Sentry error!');
    });
