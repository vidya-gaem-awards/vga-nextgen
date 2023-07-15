<?php

namespace App\Providers;

use App\BouncerScope;
use Illuminate\Support\ServiceProvider;
use Silber\Bouncer\Database\Models;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Models::scope(new BouncerScope);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
