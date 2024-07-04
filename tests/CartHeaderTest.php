<?php

namespace App\Tests;

use App\CartManager;
use App\Twig\Components\CartHeader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CartHeaderTest extends WebTestCase
{
    use Factories, InteractsWithLiveComponents, ResetDatabase;

    public function testCanShowCartHeader(): void
    {
        // Arrange
        $client = static::createClient();
        $client->disableReboot();
        $cartManager = $this->createMock(CartManager::class);
        static::getContainer()->set(CartManager::class, $cartManager);

        // Act
        $component = $this->createLiveComponent(CartHeader::class, [], $client);
        $this->assertStringContainsString('Panier (0)', $component->render());

        $cartManager->method('total')->willReturn(19);
        $component->emit('refreshCart');

        // Assert
        $this->assertStringContainsString('Panier (19)', $component->render());
    }
}
