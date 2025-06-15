<?php

namespace App\Tests;

use App\CartManager;
use App\Twig\Components\CartHeader;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CartHeaderTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanShowCartHeader(): void
    {
        // Act
        $component = $this->createLiveComponent(CartHeader::class, ['quantity' => 18]);
        $this->assertStringContainsString('18', $component->render());
        $component->emit('refreshCart', ['quantity' => 22]);

        // Assert
        $this->assertStringContainsString('22', $component->render());
    }

    public function testCanShowCartHeaderWithMock(): void
    {
        // Arrange
        $this->client->disableReboot();
        $cartManager = $this->createMock(CartManager::class);
        $cartManager->method('quantity')->willReturn(22);
        static::getContainer()->set(CartManager::class, $cartManager);

        // Act
        $component = $this->createLiveComponent(CartHeader::class, [], $this->client);

        // Assert
        $this->assertStringContainsString('22', $component->render());
        $this->assertEquals(22, static::getContainer()->get(CartManager::class)->quantity());
    }
}
