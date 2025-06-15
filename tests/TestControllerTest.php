<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TestControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->ensureClientIsInitialized();
    }

    protected ?AbstractBrowser $client = null;

    protected function ensureClientIsInitialized(): void
    {
        if (!$this->client instanceof AbstractBrowser) {
            $this->client = static::createClient();
        }
    }

    protected function get(string $uri): object
    {
        $this->ensureClientIsInitialized();

        $this->client->request('GET', $uri);

        return $this->client->getResponse();
    }

    public function testHomeCanBeReached(): void
    {
        $this->get('/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Immo');
    }

    public function testFormCanBeSendWhenValid(): void
    {
        $this->get('/');

        self::getClient()->submitForm('Envoyer', [
            'form[name]' => 'Abc',
            'form[message]' => 'Def',
        ]);

        self::getClient()->followRedirect();
        $this->assertSelectorTextContains('.success', 'Merci Abc');
    }

    public function testFormShowErrorsWhenInvalid(): void
    {
        $this->get('/');
        self::getContainer()->get(RequestStack::class)->push(self::getClient()->getRequest());

        self::getClient()->request('POST', '/', [
            'form' => [
                'name' => '',
                'message' => '',
                '_token' => self::getContainer()->get(CsrfTokenManagerInterface::class)->getToken('form')->getValue(),
            ],
        ]);

        $this->assertSelectorTextContains('li', 'This value should not be blank.');
    }
}
