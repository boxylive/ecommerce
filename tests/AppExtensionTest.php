<?php

namespace App\Tests;

use App\Twig\Extension\AppExtension;
use App\Twig\Runtime\AppExtensionRuntime;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

class AppExtensionTest extends IntegrationTestCase
{
    public function getExtensions(): array
    {
        return [new AppExtension()];
    }

    protected function getRuntimeLoaders(): array
    {
        return [
            new FactoryRuntimeLoader([
                AppExtensionRuntime::class => function () {
                    return new AppExtensionRuntime();
                },
            ]),
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__.'/Twig/';
    }
}
