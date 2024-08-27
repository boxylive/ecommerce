<?php

namespace App\Tests\Admin;

use App\Factory\UserFactory;
use App\Tests\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAdminCanSeeAdminProducts(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com', 'roles' => ['ROLE_ADMIN']]);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/admin/produits');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Produits');
    }

    public function testUserCannotSeeAdminProducts(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/admin/produits');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGuestCannotSeeAdminProducts(): void
    {
        // Act
        $this->client->request('GET', '/admin/produits');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }
}
