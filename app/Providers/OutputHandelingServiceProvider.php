<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\OutputHandeling;

class OutputHandelingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Connection::class, function ($app) {
            return new OutputHandeling();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
