<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class HomeControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testCanSeeHomepage(): void
    {
        // Arrange
        ProductFactory::createSequence([
            ['name' => 'Produit 1', 'description' => 'Dolor totam quidem itaque ipsum sequi odio deleniti libero suscipit'],
            ['name' => 'Produit 2', 'price' => 1000],
        ]);

        // Act
        $crawler = $this->client->request('GET', '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Ecommerce', $crawler->text());
        $this->assertSelectorTextContains('h3', 'Produit 1');
        $this->assertStringContainsString('Dolor totam quidem itaque ipsum sequi odio deleniti...', $crawler->text());
        $this->assertEquals('Produit 2', $crawler->filter('h3')->last()->text());
        $this->assertStringContainsString('12,00 € TTC', $crawler->text());
    }
}
