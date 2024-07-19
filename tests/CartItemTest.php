<?php

namespace App\Tests;

use App\Factory\CartItemFactory;
use App\Repository\CartItemRepository;
use App\Twig\Components\CartItem;
use Symfony\Component\BrowserKit\Cookie;
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

        // Act
        // Put session before test request
        $session = static::getContainer()->get('session.factory')->createSession();
        $session->set('cart', $cartItem->getCart()->getId());
        $session->save();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $component = $this->createLiveComponent(CartItem::class, [
            'cartItem' => $cartItem->_real(),
            'quantity' => $cartItem->getQuantity(),
        ], $this->client);
        $component->set('quantity', 2);
        $component->call('update');

        $this->assertEquals(2, $cartItem->_refresh()->getQuantity());
        $eventsToEmit = json_decode($this->client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 2], $eventsToEmit[0]['data']);
    }

    public function testCanRemoveProductFromCart(): void
    {
        // Arrange
        $cartItem = CartItemFactory::createOne(['quantity' => 1]);

        // Act
        // Put session before test request
        $session = static::getContainer()->get('session.factory')->createSession();
        $session->set('cart', $cartItem->getCart()->getId());
        $session->save();
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        $component = $this->createLiveComponent(CartItem::class, [
            'cartItem' => $cartItem->_real(),
            'quantity' => $cartItem->getQuantity(),
        ], $this->client);
        $component->call('remove');

        $this->assertNull(static::getContainer()->get(CartItemRepository::class)->find($cartItem->getId()));
        $eventsToEmit = json_decode($this->client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 0], $eventsToEmit[0]['data']);
    }
}
