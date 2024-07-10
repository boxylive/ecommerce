<?php

namespace App\Tests;

use App\Twig\Components\CartHeader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        $component = $this->createLiveComponent(CartHeader::class, ['quantity' => 0]);
        $this->assertStringContainsString('Panier (0)', $component->render());
        $component->emit('refreshCart', ['quantity' => 19]);

        // Assert
        $this->assertStringContainsString('Panier (19)', $component->render());
    }
}
