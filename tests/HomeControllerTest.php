<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        static::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('span', 'Ecommerce');
        $this->assertSelectorTextContains('h3', 'Produit 1');
        $this->assertStringContainsString('Dolor totam quidem itaque ipsum sequi odio deleniti...', $crawler->text());
        $this->assertEquals('Produit 2', $crawler->filter('h3')->last()->text());
        $this->assertStringContainsString('12,00 € TTC', $crawler->text());
    }
}
