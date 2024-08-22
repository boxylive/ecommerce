<?php

namespace App\Dto;

use App\Entity\User as EntityUser;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    #[Assert\Email]
    public string $email;

    #[SecurityAssert\UserPassword]
    public ?string $currentPassword = null;

    #[Assert\PasswordStrength(
        minScore: Assert\PasswordStrength::STRENGTH_WEAK
    )]
    public ?string $plainPassword = null;

    public static function fromEntity(EntityUser $entity): self
    {
        $user = new self();
        $user->email = $entity->getEmail();

        return $user;
    }
}
