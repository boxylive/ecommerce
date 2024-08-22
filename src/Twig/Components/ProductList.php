<?php

namespace App\Twig\Components;

use App\Repository\ProductRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ProductList
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public int $page = 1;

    #[LiveProp]
    public int $perPage = 8;

    public function __construct(private ProductRepository $repository)
    {
    }

    #[LiveAction]
    public function load()
    {
        ++$this->page;
    }

    public function getProducts()
    {
        return $this->repository->paginate($this->page, $this->perPage);
    }

    public function hasMore()
    {
        return count($this->getProducts()) > 0;
    }
}
