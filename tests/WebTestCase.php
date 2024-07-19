<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestWebTestCase;

class WebTestCase extends TestWebTestCase
{
    protected ?KernelBrowser $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }
}
