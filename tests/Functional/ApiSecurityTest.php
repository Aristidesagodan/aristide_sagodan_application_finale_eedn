<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiSecurityTest extends WebTestCase
{
    public function testApiDeniedForAnonymousUser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/cities/Paris/pois');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testApiAllowedForAuthenticatedUser(): void
    {
        $client = static::createClient();

        // Simule un utilisateur connecté
        $client->loginUser(
            $this->createTestUser()
        );

        $client->request('GET', '/api/cities/Paris/pois');

        $this->assertResponseIsSuccessful();
    }

    private function createTestUser()
    {
        $user = new \App\Entity\User();
        $user->setEmail('test@test.com');
        $user->setPassword('fake'); // OK pour test
        $user->setRoles(['ROLE_USER']);

        return $user;
    }
}
