<?php

namespace App\Service;

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;

class AmoClientProvider
{
    private AmoCRMApiClient $client;
    private string $accessFilePath;

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $redirectUri,
        private readonly string $varPath,
        public readonly string $baseDomain,
    ) {
        $this->init();
    }

    private function init(): void
    {
        $this->client = new AmoCRMApiClient($this->clientId, $this->clientSecret, $this->redirectUri);
        $this->client->onAccessTokenRefresh(function () {
            $this->save();
        });
        $this->accessFilePath = $this->varPath.'/access.json';
    }

    public function getClient(): AmoCRMApiClient
    {
        $this->load();

        return $this->client;
    }

    public function save(): void
    {
        file_put_contents($this->accessFilePath, json_encode($this->client->getAccessToken()));
    }

    public function load(): void
    {
        if (null === $this->client->getAccessToken() && file_exists($this->accessFilePath)) {
            $contents = file_get_contents($this->accessFilePath);
            $accessTokenJson = json_decode($contents, true);

            $accessToken = new AccessToken($accessTokenJson);
            $this->client->setAccountBaseDomain($this->baseDomain);
            $this->client->setAccessToken($accessToken);
        }
    }

    public function shouldLogin(): bool
    {
        return $this->client->getAccessToken()->hasExpired();
    }
}
