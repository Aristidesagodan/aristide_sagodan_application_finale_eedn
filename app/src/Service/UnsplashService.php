<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class UnsplashService
{
    private HttpClientInterface $client;
    private string $accessKey;

    public function __construct(HttpClientInterface $client, string $unsplashAccessKey)
    {
        $this->client = $client;
        $this->accessKey = $unsplashAccessKey;
    }

    public function getImage(string $query): ?string
    {
        try {
            $response = $this->client->request('GET', 'https://api.unsplash.com/search/photos', [
                'query' => [
                    'query' => $query . ' France',
                    'per_page' => 1,
                ],
                'headers' => [
                    'Authorization' => 'Client-ID ' . $this->accessKey,
                ],
            ]);

            $data = $response->toArray();

            if (!isset($data['results'][0]['urls']['regular'])) {
                return null;
            }

            return $data['results'][0]['urls']['regular'];

        } catch (TransportExceptionInterface|ClientExceptionInterface|ServerExceptionInterface $e) {
            // Loguer l'erreur si nécessaire
            return null;
        }
    }
}
