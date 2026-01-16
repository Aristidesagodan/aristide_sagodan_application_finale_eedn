<?php

namespace App\Tests\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Vérifie que l'admin est protégé
 */
class AdminAccessTest extends WebTestCase
{
    public function testAdminRequiresAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        // Redirection vers login
        $this->assertResponseRedirects('/login');
    }
}
