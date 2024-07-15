<?php

namespace App\EventListener;

use App\CartManager;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class LoginListener
{
    public function __construct(
        private CartManager $cartManager,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        // @todo and if user has a cart in db ?
        $user = $event->getUser();

        if ($id = $this->cartManager->fromSession()) {
            if ($cart = $this->entityManager->getRepository(Cart::class)->find($id)) {
                $cart->setUser($user);
                $this->entityManager->flush();
            }
        }
    }
}
