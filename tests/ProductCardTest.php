<?php

namespace App\Tests;

use App\CartManager;
use App\Factory\ProductFactory;
use App\Repository\CartRepository;
use App\Twig\Components\ProductCard;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ProductCardTest extends WebTestCase
{
    use Factories, InteractsWithLiveComponents, ResetDatabase;

    public function testCanAddProductToCart(): void
    {
        // Arrange
        $product = ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        static::ensureKernelShutdown();
        $client = static::createClient(); // We need to create a client to share sessions in test

        $component = $this->createLiveComponent(ProductCard::class, [
            'product' => $product->_real(),
        ], $client);
        $component->call('add')->call('add');

        // Assert
        $cart = static::getContainer()->get(CartRepository::class)->find(1);
        $this->assertInstanceOf(\DateTimeImmutable::class, $cart->getCreatedAt());
        $this->assertEquals(1, $cart->getCartItems()->count());
        $item = $cart->getCartItems()[0];
        $this->assertEquals(2, $item->getQuantity());
        $this->assertEquals($product->_real()->getId(), $item->getProduct()->getId());

        $requestStack = static::getContainer()->get(RequestStack::class);
        $requestStack->push($client->getRequest());
        $this->assertEquals(2, static::getContainer()->get(CartManager::class)->quantity());
        $this->assertEquals($cart->getId(), $requestStack->getSession()->get('cart'));

        $eventsToEmit = json_decode($client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 2], $eventsToEmit[0]['data']);
    }
}
