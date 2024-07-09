<?php

namespace App\Twig\Components;

use App\Entity\Product;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ProductCard
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public Product $product;
}
