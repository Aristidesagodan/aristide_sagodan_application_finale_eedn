<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test fonctionnel de l'API villes → POI
 */
class CityPoiControllerTest extends WebTestCase
{
    public function testCityPoisEndpoint(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/cities/Paris/pois');

        // Vérifie HTTP 200
        $this->assertResponseIsSuccessful();

        // Vérifie JSON
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('city', $data);
        $this->assertArrayHasKey('activities', $data);
        $this->assertArrayHasKey('accommodations', $data);

        // Max 10 résultats
        $this->assertLessThanOrEqual(10, count($data['activities']));
        $this->assertLessThanOrEqual(10, count($data['accommodations']));
    }
}
