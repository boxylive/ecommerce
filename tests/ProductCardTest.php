<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use App\Twig\Components\ProductCard;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductCardTest extends WebTestCase
{
    use Factories, InteractsWithLiveComponents, ResetDatabase;

    public function testCanSeeProduct(): void
    {
        // Arrange
        $product = ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient(); // We need to create a client to share sessions in test

        $component = $this->createLiveComponent(ProductCard::class, [
            'product' => $product->_real(),
        ], $client);

        // Assert
        $this->assertStringContainsString('Produit 1', $render = $component->render());
        $this->assertStringContainsString('Ajouter au panier', $render);
        $this->assertStringContainsString('22,79 € TTC', $render);
    }
}
