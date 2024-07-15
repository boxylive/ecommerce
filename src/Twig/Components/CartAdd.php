<?php

namespace App\Twig\Components;

use App\CartManager;
use App\Entity\Product;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class CartAdd
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public Product $product;

    #[LiveProp(writable: true)]
    public bool $added = false;

    #[LiveAction]
    public function add(CartManager $cartManager)
    {
        $cartManager->add($this->product, 1);

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
