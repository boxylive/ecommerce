<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCanGetAvatar()
    {
        $user = new User();

        $this->assertNull($user->getAvatar());

        $user->setEmail('fiorella@boxydev.com');
        $this->assertEquals('https://unavatar.io/fiorella@boxydev.com?fallback=https%3A%2F%2Fui-avatars.com%2Fapi%2F%3Fname%3Dfiorella', $user->getAvatar());
    }
}
