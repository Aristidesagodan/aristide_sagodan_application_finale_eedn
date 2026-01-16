<?php

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class LoginTest extends WebTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = self::getContainer()->get('doctrine')->getManager();

        // Nettoyage utilisateur test s'il existe
        $existing = $this->em->getRepository(User::class)
            ->findOneBy(['email' => 'test@login.com']);

        if ($existing) {
            $this->em->remove($existing);
            $this->em->flush();
        }

        // Création utilisateur test
        $user = new User();
        $user->setEmail('test@login.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            self::getContainer()->get('security.password_hasher')
                ->hashPassword($user, 'password123')
        );

        $this->em->persist($user);
        $this->em->flush();
    }

    public function testLoginSuccess(): void
    {
        $client = static::createClient();

        // Accès page login
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // Soumission du formulaire
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@login.com',
            'password' => 'password123',
        ]);

        $client->submit($form);
        $client->followRedirect();

        // Vérification utilisateur connecté
        $this->assertSelectorExists('a[href="/logout"]');
    }

    public function testLoginFailure(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@login.com',
            'password' => 'wrongpassword',
        ]);

        $client->submit($form);

        $this->assertSelectorTextContains('.alert-danger', 'Identifiants invalides');
    }
}
