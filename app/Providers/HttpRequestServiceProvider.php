<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\HttpRequest;


class HttpRequestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Connection::class, function ($app) {
            return new HttpRequest();
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
