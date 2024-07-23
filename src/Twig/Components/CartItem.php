<?php

namespace App\Twig\Components;

use App\CartManager;
use App\Entity\CartItem as EntityCartItem;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class CartItem
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp]
    public EntityCartItem $cartItem;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $quantity;

    #[LiveAction]
    public function update(CartManager $cartManager)
    {
        $this->validate();

        $cartManager->update($this->cartItem->getId(), $this->quantity);

        $this->emit('refreshCart', [
            'quantity' => $cartManager->quantity(),
        ]);
    }

    #[LiveAction]
    public function remove(CartManager $cartManager)
    {
        $cartManager->remove($this->cartItem->getId());

        $this->emit('refreshCart', [
            'quantity' => $cartManager->quantity(),
        ]);
    }
}
