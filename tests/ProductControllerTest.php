<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductControllerTest extends WebTestCase
{
    use Factories, ResetDatabase;

    public function testCanSeeAProduct(): void
    {
        // Arrange
        ProductFactory::createSequence([
            ['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899],
        ]);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/produit-1');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Produit 1');
        $this->assertStringContainsString('22,79 € TTC', $crawler->text());
    }
}
