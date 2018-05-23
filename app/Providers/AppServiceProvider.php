<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //for migrations
        Schema::defaultStringLength(191);

        //for shopify
        $forceSchema = (env('FORCESCHEMA') !== null) ? env('FORCESCHEMA') : 'https';

        URL::forceScheme($forceSchema);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
