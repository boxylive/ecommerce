<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class CartHeader
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?int $quantity = null;

    #[LiveListener('refreshCart')]
    public function refreshCart(#[LiveArg] int $quantity)
    {
        $this->quantity = $quantity;
    }
}
