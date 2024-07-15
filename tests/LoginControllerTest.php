<?php

namespace App\Tests;

use App\Factory\CartFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
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

    public function testUserCanLoginWithACartInSession(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $cart = CartFactory::createOne();

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();

        // Put session before test request
        $session = static::getContainer()->get('session.factory')->createSession();
        $session->set('cart', $cart->getId());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $client->request('GET', '/login');
        $client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'password',
        ]);

        // Assert
        // @todo why flush ?
        $this->assertEquals($user->getId(), $cart->getUser()->getId());
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
