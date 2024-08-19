<?php

namespace App\EventListener;

use App\CartManager;
use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class LoginListener
{
    public function __construct(
        private CartManager $cartManager,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if ($id = $this->cartManager->fromSession()) {
            $cart = $this->cartManager->current();

            // User has already a cart
            if ($cart->getUser() && $cart->getUser() === $user) {
                /** @var Cart $cartFromSession */
                $cartFromSession = $this->entityManager->getRepository(Cart::class)->findWithItems($id);

                foreach ($cartFromSession->getCartItems() as $cartItem) {
                    $this->cartManager->add($cartItem->getProduct(), $cartItem->getQuantity());
                }

                $this->entityManager->remove($cartFromSession);
                $this->requestStack->getSession()->remove('cart');
            } else {
                $cart->setUser($user);
            }

            $this->entityManager->flush();
        }
    }
}
