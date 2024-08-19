<?php

namespace App\Tests;

use App\Factory\CartFactory;
use App\Factory\CartItemFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Repository\CartRepository;
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
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'password',
        ]);

        // Assert
        $this->assertResponseRedirects('/');
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('fiorella@boxydev.com', $crawler->text());
        $this->assertResponseIsSuccessful();
    }

    public function testUserCanLoginWithACartInSession(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $cart = CartFactory::createOne();
        CartItemFactory::createOne(['cart' => $cart]);
        $this->mockSession(function ($session) use ($cart) {
            $session->set('cart', $cart->getId());
        });

        // Act
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'password',
        ]);

        // Assert
        $this->assertEquals($user->getId(), $cart->getUser()->getId());
    }

    public function testUserCanLoginWithACartAndACartInSession(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $cart = CartFactory::createOne(['user' => $user]);
        $cartInSession = CartFactory::createOne(['user' => null]);

        $product1 = ProductFactory::createOne(['price' => 500]);
        $product2 = ProductFactory::createOne(['price' => 1000]);

        CartItemFactory::createOne(['product' => $product1, 'quantity' => 1, 'cart' => $cart]);
        CartItemFactory::createOne(['product' => $product1, 'quantity' => 2, 'cart' => $cartInSession]);
        CartItemFactory::createOne(['product' => $product2, 'quantity' => 1, 'cart' => $cartInSession]);

        $this->mockSession(function ($session) use ($cartInSession) {
            $session->set('cart', $cartInSession->getId());
        });

        // Act
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'password',
        ]);

        // Assert
        $this->assertNull(static::getContainer()->get(CartRepository::class)->find($cartInSession->getId()));
        $items = $cart->_real()->getCartItems();
        $this->assertEquals(3, $items->first()->getQuantity());
        $this->assertEquals(1, $items->last()->getQuantity());
    }

    public function testUserCannotLoginWithErrors(): void
    {
        // Arrange
        UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', [
            '_username' => 'fiorella@boxydev.com',
            '_password' => 'invalid',
        ]);

        // Assert
        $this->assertResponseRedirects('/login');
        $crawler = $this->client->followRedirect();
        $this->assertStringContainsString('Identifiants invalides.', $crawler->text());
    }

    public function testUserCanLogout(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/logout');
        $crawler = $this->client->followRedirect();

        // Assert
        $this->assertStringNotContainsString('fiorella@boxydev.com', $crawler->text());
    }
}
