<?php

namespace App\Providers\Socialite;

use SocialiteProviders\Steam\Provider;

class SteamProvider extends Provider
{
    protected function getAuthUrl($state)
    {
        return $this->buildUrl();
    }

    private function buildUrl()
    {
        $realm = $this->getConfig('realm', $this->request->server('HTTP_HOST'));

        $params = [
            'openid.ns'         => self::OPENID_NS,
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $this->parameters['redirect_uri'] ?? $this->redirectUrl,
            'openid.realm'      => sprintf('%s://%s', $this->request->getScheme(), $realm),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        return self::OPENID_URL.'?'.http_build_query($params, '', '&');
    }
}
