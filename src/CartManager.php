<?php

namespace App;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartManager
{
    /**
     * @todo check why Doctrine don't use unitOfWork in Repository
     */
    private $cart;

    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Determine if cart is empty.
     */
    public function empty(): bool
    {
        return 0 === $this->quantity();
    }

    /**
     * Return total quantity in cart.
     */
    public function quantity(): int
    {
        if (!$this->fromSession()) {
            return 0;
        }

        return $this->current()->getCartItems()->reduce(function (int $accumulator, CartItem $cartItem): int {
            return $accumulator + $cartItem->getQuantity();
        }, 0);
    }

    /**
     * Return total price in cart.
     */
    public function total(): int
    {
        if (!$this->fromSession()) {
            return 0;
        }

        return $this->current()->getCartItems()->reduce(function (int $accumulator, CartItem $cartItem): int {
            return $accumulator + $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();
        }, 0);
    }

    /**
     * Find an item in cart.
     */
    protected function findItem($product): ?CartItem
    {
        return $this->current()->getCartItems()->findFirst(function (int $key, CartItem $cartItem) use ($product): bool {
            return $cartItem->getProduct()->getId() === $product->getId();
        });
    }

    /**
     * Return cart id from session.
     */
    public function fromSession(): ?int
    {
        $session = $this->requestStack->getSession();

        return $session->get('cart');
    }

    /**
     * Return current cart for user.
     */
    public function current(): Cart
    {
        if ($id = $this->fromSession()) {
            $this->cart = $this->cart ?: $this->entityManager->getRepository(Cart::class)
                ->findWithItems($id);

            if ($this->cart) {
                return $this->cart;
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

        if ($cartItem = $this->findItem($product)) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setQuantity($quantity);
            $cart->addCartItem($cartItem);
            $cartItem->setProduct($product);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();

        $session = $this->requestStack->getSession();
        $session->set('cart', $cart->getId());
    }

    /**
     * Update an item in cart.
     */
    public function update(int $id, int $quantity): void
    {
        $cart = $this->current();

        /** @var CartItem */
        $cartItem = $cart->getCartItems()->findFirst(function (int $key, CartItem $cartItem) use ($id): bool {
            return $cartItem->getId() === $id;
        });

        if ($cartItem) {
            $cartItem->setQuantity($quantity);

            $this->entityManager->flush();
        }
    }

    /**
     * Remove an item from cart.
     */
    public function remove(int $id): void
    {
        $cartItem = $this->entityManager->getReference(CartItem::class, $id);
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }
}
