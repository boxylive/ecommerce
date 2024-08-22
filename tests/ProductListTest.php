<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use App\Twig\Components\ProductList;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductListTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanPaginateProducts(): void
    {
        // Arrange
        ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);
        ProductFactory::createOne(['name' => 'Produit 2', 'slug' => 'produit-2', 'price' => 999]);
        ProductFactory::createOne(['name' => 'Produit 3', 'slug' => 'produit-3', 'price' => 1499]);

        // Act
        $component = $this->createLiveComponent(ProductList::class, [
            'perPage' => 2,
        ], $this->client);

        // Assert
        $this->assertStringContainsString('Produit 1', $render = $component->render());
        $this->assertStringNotContainsString('Produit 3', $render);
        $this->assertTrue($component->component()->hasMore());

        $component->call('load');

        $this->assertStringNotContainsString('Produit 1', $render = $component->render());
        $this->assertStringContainsString('Produit 3', $component->render());
        $this->assertTrue($component->component()->hasMore());

        $component->call('load');

        $this->assertFalse($component->component()->hasMore());
    }
}
