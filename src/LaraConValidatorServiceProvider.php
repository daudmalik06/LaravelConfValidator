<?php

namespace Dawood\LaravelConfValidator;

use Illuminate\Support\ServiceProvider;

class LaraConValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/user.php' => config_path('validation/user.php')
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/helper.php';
    }
}
