<?php

namespace App\Twig\Components\Admin;

use App\Repository\ProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ProductManager
{
    use DefaultActionTrait;

    #[LiveProp(url: true)]
    public int $page = 1;

    #[LiveProp]
    public int $byPage = 10;

    #[LiveProp]
    public int $total;

    #[LiveProp(url: true)]
    public string $field = 'name';

    #[LiveProp(url: true)]
    public string $order = 'asc';

    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function getProducts(): array
    {
        return $this->productRepository->paginate($this->page, $this->byPage, $this->field, $this->order);
    }
}
