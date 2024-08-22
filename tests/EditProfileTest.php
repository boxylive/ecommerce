<?php

namespace App\Tests;

use App\Factory\UserFactory;
use App\Twig\Components\EditProfile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class EditProfileTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testUserCanEditProfile(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $this->mockSession();

        // Act
        $component = $this->createLiveComponent(EditProfile::class, [], $this->client)->actingAs($user->_real());
        $oldHash = $user->getPassword();
        $oldCurrentUser = $component->component()->currentUser;
        $component->submitForm(['edit_profile' => [
            'email' => 'matthieu@boxydev.com',
            'currentPassword' => 'password',
            'plainPassword' => [
                'first' => 'MonSuperMotDePasse',
                'second' => 'MonSuperMotDePasse',
            ],
        ]], 'save');

        // Assert
        $this->assertEquals('fiorella@boxydev.com', $oldCurrentUser->email);
        $this->assertSame(302, $component->response()->getStatusCode());
        $this->assertEquals('matthieu@boxydev.com', $user->getEmail());
        $this->assertNotEquals($oldHash, $user->getPassword());

        /** @var FlashBag $flashBag */
        $flashBag = $this->client->getRequest()->getSession()->getFlashBag();

        $this->assertCount(1, $flashBag->peek('success'));
    }

    public function testUserCanEditEmailOnly(): void
    {
        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $this->mockSession();

        // Act
        $component = $this->createLiveComponent(EditProfile::class)->actingAs($user->_real());
        $oldHash = $user->getPassword();
        $component->submitForm(['edit_profile' => [
            'email' => 'matthieu@boxydev.com',
            'currentPassword' => 'password',
        ]], 'save');

        // Assert
        $this->assertSame(302, $component->response()->getStatusCode());
        $this->assertEquals('matthieu@boxydev.com', $user->getEmail());
        $this->assertEquals($oldHash, $user->getPassword());
    }

    public function testUserCannotEditProfileWithoutPassword(): void
    {
        $this->expectException(UnprocessableEntityHttpException::class);

        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $this->mockSession();

        // Act
        $component = $this->createLiveComponent(EditProfile::class)->actingAs($user->_real());
        $component->submitForm(['edit_profile' => [
            'email' => 'matthieu@boxydev.com',
            'currentPassword' => 'wrong',
        ]], 'save');

        // Assert
    }

    public function testUserCannotEditPasswordWithoutConfirmation(): void
    {
        $this->expectException(UnprocessableEntityHttpException::class);

        // Arrange
        $user = UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        $this->mockSession();

        // Act
        $component = $this->createLiveComponent(EditProfile::class)->actingAs($user->_real());
        $component->submitForm(['edit_profile' => [
            'email' => 'matthieu@boxydev.com',
            'currentPassword' => 'password',
            'plainPassword' => [
                'first' => 'MonSuperMotDePasse',
                'second' => 'Rien',
            ],
        ]], 'save');

        // Assert
    }
}
