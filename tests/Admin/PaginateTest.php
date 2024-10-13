<?php

namespace App\Tests\Admin;

use App\Tests\WebTestCase;
use App\Twig\Components\Admin\Paginate;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class PaginateTest extends WebTestCase
{
    use Factories;
    use InteractsWithLiveComponents;
    use ResetDatabase;

    public function testCanPaginate(): void
    {
        // Act
        $component = $this->createLiveComponent(Paginate::class, [
            'total' => 18,
            'url' => 'home',
        ], $this->client);

        // Assert
        $render = $component->render();
        $this->assertStringNotContainsString('Précédent', $render);
        $this->assertStringContainsString('href="/?page=1"', $render);
        $this->assertStringContainsString('href="/?page=18"', $render);
        $this->assertStringContainsString('Suivant', $render);
    }
}
