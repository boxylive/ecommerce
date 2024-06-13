<?php

namespace App\Tests;

use App\Factory\ProductFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class HomeControllerTest extends WebTestCase
{
    use Factories, ResetDatabase;

    public function testCanSeeHomepage(): void
    {
        // Arrange
        ProductFactory::createSequence([
            ['name' => 'Produit 1'],
            ['name' => 'Produit 2'],
        ]);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('span', 'Ecommerce');
        $this->assertSelectorTextContains('h2', 'Produit 1');
        $this->assertEquals('Produit 2', $crawler->filter('h2')->last()->text());
    }
}
