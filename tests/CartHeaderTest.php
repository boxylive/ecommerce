<?php

namespace App\Tests;

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
}
