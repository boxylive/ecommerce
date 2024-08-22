<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testUserCanSeeAccount(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);

        // Act
        $this->client->loginUser($user->_real());
        $this->client->request('GET', '/mon-compte');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue fiorella@boxydev.com');
    }

    public function testGuestCannotSeeAccount(): void
    {
        // Act
        $this->client->request('GET', '/mon-compte');

        // Assert
        $this->assertResponseRedirects('/login');
    }
}
