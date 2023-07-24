<?php

namespace DoubleA\LaravelInit\Providers;

use DoubleA\LaravelInit\Console\Commands\Init;
use Illuminate\Support\ServiceProvider;

class InitServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([Init::class]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/init.php' =>  config_path('init.php'),
        ], 'config');
    }
}
