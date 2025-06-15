<?php

namespace App\Twig\Components;

use App\CartManager;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class CartAdd
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp]
    public Product $product;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public ?int $quantity = null;

    #[LiveProp]
    public bool $added = false;

    #[LiveAction]
    public function add(CartManager $cartManager)
    {
        $this->validate();

        $cartManager->add($this->product, $this->quantity ?? 1);

        $this->added = true;

        $this->emit('refreshCart', [
            'quantity' => $cartManager->quantity(),
        ]);
    }

    #[LiveAction]
    public function reset()
    {
        $this->added = false;
    }
}
