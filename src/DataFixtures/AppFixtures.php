<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\ImagesRetriever;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private ImagesRetriever $retriever)
    {
    }

    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(['email' => 'fiorella@boxydev.com']);
        UserFactory::createOne(['email' => 'matthieu@boxydev.com', 'roles' => ['ROLE_ADMIN']]);

        // $images = $this->retriever->getImages();

        $images = [
            'https://tailwindui.com/plus/img/ecommerce-images/product-page-01-related-product-01.jpg',
            'https://tailwindui.com/plus/img/ecommerce-images/product-page-01-related-product-02.jpg',
            'https://tailwindui.com/plus/img/ecommerce-images/product-page-01-related-product-03.jpg',
            'https://tailwindui.com/plus/img/ecommerce-images/product-page-01-related-product-04.jpg',
        ];

        ProductFactory::createMany(100, function () use ($images) {
            return ['image' => $this->retriever->random($images)];
        });
    }
}
