<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCanSeeAProduct(): void
    {
        // Arrange
        ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        $crawler = $this->client->request('GET', '/produit-1');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Produit 1');
        $this->assertStringContainsString('22,79 €', $crawler->text());
    }
}
