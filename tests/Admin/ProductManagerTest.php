<?php

namespace App\Tests\Admin;

use App\Factory\ProductFactory;
use App\Tests\WebTestCase;
use App\Twig\Components\Admin\ProductManager;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductManagerTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanListProduct(): void
    {
        // Arrange
        ProductFactory::createOne(['name' => 'Produit A']);
        ProductFactory::createOne(['name' => 'Produit B']);

        // Act
        $component = $this->createLiveComponent(ProductManager::class, [
            'byPage' => 1,
            'total' => 2,
            'field' => 'name',
            'order' => 'desc',
        ], $this->client);

        // Assert
        $render = $component->render();
        $this->assertStringNotContainsString('Produit A', $render);
        $this->assertStringContainsString('Produit B', $render);
    }
}
