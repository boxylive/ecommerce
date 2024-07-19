<?php

namespace App\Tests;

use App\CartManager;
use App\Factory\ProductFactory;
use App\Repository\CartRepository;
use App\Twig\Components\CartAdd;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CartAddTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanAddProductToCart(): void
    {
        // Arrange
        $product = ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        $component = $this->createLiveComponent(CartAdd::class, [
            'product' => $product->_real(),
        ], $this->client);
        $component->call('add')->call('add');

        // Assert
        $cart = static::getContainer()->get(CartRepository::class)->find(1);
        $this->assertInstanceOf(\DateTimeImmutable::class, $cart->getCreatedAt());
        $this->assertEquals(1, $cart->getCartItems()->count());
        $item = $cart->getCartItems()[0];
        $this->assertEquals(2, $item->getQuantity());
        $this->assertEquals($product->_real()->getId(), $item->getProduct()->getId());

        $requestStack = static::getContainer()->get(RequestStack::class);
        $requestStack->push($this->client->getRequest());
        $this->assertEquals(2, static::getContainer()->get(CartManager::class)->quantity());
        $this->assertEquals($cart->getId(), $requestStack->getSession()->get('cart'));

        $this->assertStringContainsString('Le produit a bien été ajouté', $component->render());
    }

    public function testAddAProductEmitEvent(): void
    {
        // Arrange
        $product = ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        $component = $this->createLiveComponent(CartAdd::class, [
            'product' => $product->_real(),
        ], $this->client);
        $component->call('add');

        $eventsToEmit = json_decode($this->client->getCrawler()->filter('[data-live-events-to-emit-value]')->attr('data-live-events-to-emit-value'), true);

        $this->assertEquals('refreshCart', $eventsToEmit[0]['event']);
        $this->assertEquals(['quantity' => 1], $eventsToEmit[0]['data']);
    }

    public function testAddAProductManagedAdded(): void
    {
        // Arrange
        $product = ProductFactory::createOne(['name' => 'Produit 1', 'slug' => 'produit-1', 'price' => 1899]);

        // Act
        $component = $this->createLiveComponent(CartAdd::class, [
            'product' => $product->_real(),
        ], $this->client);
        $component->call('add');

        $this->assertTrue($component->component()->added);
        $component->call('reset');
        $this->assertFalse($component->component()->added);
    }
}
