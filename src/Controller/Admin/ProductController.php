<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 404)]
#[Route('/admin')]
class ProductController extends AbstractController
{
    #[Route('/produits', name: 'admin_product')]
    public function index(): Response
    {
        return $this->render('admin/product/index.html.twig');
    }
}
