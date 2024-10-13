<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 404)]
#[Route('/admin')]
class ProductController extends AbstractController
{
    #[Route('/produits', name: 'admin_product')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $page = (int) $request->get('page', 1);
        $total = $productRepository->totalPages(10);

        if ($page > 1 && $page > $total || $page <= 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/product/index.html.twig', [
            'total' => $total,
        ]);
    }
}
