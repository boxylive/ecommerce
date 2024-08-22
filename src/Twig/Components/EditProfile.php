<?php

namespace App\Twig\Components;

use App\Dto\User;
use App\Entity\User as EntityUser;
use App\Form\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class EditProfile extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public User $currentUser;

    public function __construct(private Security $security)
    {
    }

    public function mount()
    {
        $this->currentUser = User::fromEntity(
            $this->security->getUser()
        );
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditProfileType::class, $this->currentUser);
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher)
    {
        $this->submitForm();

        /** @var User $user */
        $user = $this->getForm()->getData();

        /** @var EntityUser $entity */
        $entity = $this->security->getUser();
        $entity->setEmail($user->email);

        if (!empty($user->plainPassword)) {
            $entity->setPassword(
                $hasher->hashPassword($entity, $user->plainPassword)
            );
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a bien été modifié !');

        return $this->redirectToRoute('account');
    }
}
