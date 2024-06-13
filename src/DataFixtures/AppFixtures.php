<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\ImagesRetriever;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(private ImagesRetriever $retriever) {}

    public function load(ObjectManager $manager): void
    {
        $images = $this->retriever->getImages();

        ProductFactory::createMany(100, function () use ($images) {
            return ['image' => $this->retriever->random($images)];
        });
    }
}
