<?php

namespace App\Tests;

use App\Factory\CartItemFactory;
use App\Repository\CartItemRepository;
use App\Twig\Components\CartItem;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CartItemTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanUpdateProductInCart(): void
    {
        // Arrange
        $cartItem = CartItemFactory::createOne(['quantity' => 1]);
        $this->mockSession(function ($session) use ($cartItem) {
            $session->set('cart', $cartItem->getCart()->getId());
        });

        // Act
        $component = $this->createLiveComponent(CartItem::class, [
            'cartItem' => $cartItem->_real(),
            'quantity' => $cartItem->getQuantity(),
        ], $this->client);
        $component->set('quantity', 2);
        $component->call('update');

        // Assert
        $this->assertEquals(2, $cartItem->_refresh()->getQuantity());
        $eventsToEmit = json_decode($this->client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 2], $eventsToEmit[0]['data']);
    }

    public function testCanRemoveProductFromCart(): void
    {
        // Arrange
        $cartItem = CartItemFactory::createOne(['quantity' => 1]);
        $this->mockSession(function ($session) use ($cartItem) {
            $session->set('cart', $cartItem->getCart()->getId());
        });

        // Act
        $component = $this->createLiveComponent(CartItem::class, [
            'cartItem' => $cartItem->_real(),
            'quantity' => $cartItem->getQuantity(),
        ], $this->client);
        $component->call('remove');

        // Assert
        $this->assertNull(static::getContainer()->get(CartItemRepository::class)->find($cartItem->getId()));
        $eventsToEmit = json_decode($this->client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 0], $eventsToEmit[0]['data']);
    }
}
