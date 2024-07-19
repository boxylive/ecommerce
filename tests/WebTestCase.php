<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestWebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class WebTestCase extends TestWebTestCase
{
    protected ?KernelBrowser $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function mockSession(callable $callback)
    {
        $session = static::getContainer()->get('session.factory')->createSession();
        $callback($session);
        $session->save();

        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}
