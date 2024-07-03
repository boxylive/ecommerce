<?php

namespace App\Tests;

use App\CartManager;
use App\Twig\Components\CartTotal;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CartTotalTest extends WebTestCase
{
    use Factories, InteractsWithLiveComponents, ResetDatabase;

    public function testCanShowCartTotal(): void
    {
        // Arrange
        $client = static::createClient();
        $client->disableReboot();
        $cartManager = $this->createMock(CartManager::class);
        static::getContainer()->set(CartManager::class, $cartManager);

        // Act
        $cartTotalComponent = $this->createLiveComponent(CartTotal::class, [], $client);
        $this->assertStringContainsString('Panier (0)', $cartTotalComponent->render());

        $cartManager->method('total')->willReturn(19);
        $cartTotalComponent->emit('refreshCart');

        // Assert
        $this->assertStringContainsString('Panier (19)', $cartTotalComponent->render());
    }
}
