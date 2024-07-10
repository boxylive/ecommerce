<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class LoginControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserCanLogin(): void
    {
        // Arrange
        UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'password',
        ]);

        // Assert
        $this->assertResponseRedirects('/');
        $crawler = $client->followRedirect();

        $this->assertStringContainsString('fiorella@boxydev.com', $crawler->text());
        $this->assertResponseIsSuccessful();
    }

    public function testUserCannotLoginWithErrors(): void
    {
        // Arrange
        UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'invalid',
        ]);

        // Assert
        $this->assertResponseRedirects('/login');
        $crawler = $client->followRedirect();
        $this->assertStringContainsString('Identifiants invalides.', $crawler->text());
    }

    public function testUserCanLogout(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();
        $client->loginUser($user->_real());
        $client->request('GET', '/logout');
        $crawler = $client->followRedirect();

        $this->assertStringNotContainsString('fiorella@boxydev.com', $crawler->text());
    }
}
