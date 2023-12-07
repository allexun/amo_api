<?php

namespace App\Service;

use AmoCRM\Exceptions\AmoCRMApiException;

class AmoAuthService
{
    public function __construct(
        private readonly AmoClientProvider $provider,
    ) {
    }

    public function getAuthUrl(): string
    {
        $state = bin2hex(random_bytes(128 / 8));

        return $this->provider->getClient()->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message',
        ]);
    }

    public function setAccessToken(string $code): string
    {
        try {
            $accessToken = $this->provider->getClient()->getOAuthClient()->getAccessTokenByCode($code);
            $this->provider->getClient()->setAccessToken($accessToken);
            $this->provider->save();
        } catch (AmoCRMApiException $e) {
            var_dump($e);
            exit;
        }

        return $accessToken->getToken();
    }
}
