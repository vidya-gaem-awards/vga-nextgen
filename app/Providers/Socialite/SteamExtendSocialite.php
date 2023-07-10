<?php

namespace App\Providers\Socialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class SteamExtendSocialite extends \SocialiteProviders\Steam\SteamExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('steam', SteamProvider::class);
    }
}
