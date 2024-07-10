<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_show', priority: -1)]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Product $product
    ): Response {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
