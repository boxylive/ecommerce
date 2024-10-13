<?php

namespace App\Twig\Components\Admin;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Paginate
{
    use DefaultActionTrait;

    #[LiveProp(url: true)]
    public int $page = 1;

    #[LiveProp]
    public int $total;

    #[LiveProp]
    public string $url;
}
