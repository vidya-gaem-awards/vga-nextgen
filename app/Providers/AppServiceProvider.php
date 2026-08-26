<?php

namespace App\Providers;

use App\BouncerScope;
use App\Providers\Socialite\SteamExtendSocialite;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Silber\Bouncer\Database\Models;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Route::pattern('show', '20[0-9]{2}');

        Event::listen(SocialiteWasCalled::class, [SteamExtendSocialite::class, 'handle']);
        Event::listen(SocialiteWasCalled::class, [DiscordExtendSocialite::class, 'handle']);
    }
}
