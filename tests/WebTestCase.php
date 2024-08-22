<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as TestWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class WebTestCase extends TestWebTestCase
{
    protected ?KernelBrowser $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function mockSession(?callable $callback = null)
    {
        $session = static::getContainer()->get('session.factory')->createSession();

        if ($callback) {
            $callback($session);
            $session->save();
        }

        $request = new Request();
        $request->setSession($session);
        self::getContainer()->get(RequestStack::class)->push($request);

        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}
