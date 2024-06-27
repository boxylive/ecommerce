<?php

namespace App;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartManager
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * Return total quantity in cart.
     */
    public function total(): int
    {
        $session = $this->requestStack->getSession();

        if (! $session->has('cart')) {
            return 0;
        }

        return $this->current()->getCartItems()->reduce(function (int $accumulator, CartItem $cartItem): int {
            return $accumulator + $cartItem->getQuantity();
        }, 0);
    }

    /**
     * Return current cart for user.
     */
    public function current(): Cart
    {
        $session = $this->requestStack->getSession();

        if ($session->has('cart')) {
            $cart = $this->entityManager->getRepository(Cart::class)
                ->find($session->get('cart'));

            if ($cart) {
                return $cart;
            }
        }

        // @todo Clean up cart after X time...
        return new Cart();
    }

    /**
     * Add an item in cart.
     */
    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->current();

        /** @var CartItem */
        $cartItem = $cart->getCartItems()->findFirst(function (int $key, CartItem $cartItem) use ($product): bool {
            return $cartItem->getProduct()->getId() === $product->getId();
        });

        if (! $cartItem) {
            $cartItem = new CartItem();
            $cartItem->setQuantity($quantity);
            $cart->addCartItem($cartItem);
            $cartItem->setProduct($product);
        } else {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();

        $session = $this->requestStack->getSession();
        $session->set('cart', $cart->getId());
    }
}
