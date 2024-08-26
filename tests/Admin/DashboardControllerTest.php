<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class DashboardControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testAdminCanSeeAdmin(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com', 'roles' => ['ROLE_ADMIN']]);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Dashboard');
    }

    public function testUserCannotSeeAdmin(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGuestCannotSeeAdmin(): void
    {
        // Act
        $this->client->request('GET', '/admin');

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }
}
