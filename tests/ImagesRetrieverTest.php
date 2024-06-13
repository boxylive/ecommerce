<?php

namespace App\Tests;

use App\ImagesRetriever;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ImagesRetrieverTest extends TestCase
{
    public function testAPICanRespondWithInvalidJsonOrEmptyBody(): void
    {
        $imagesRetriever = new ImagesRetriever(
            new MockHttpClient([
                new MockResponse(),
                new MockResponse(json_encode([])),
            ])
        );

        $this->assertEmpty($imagesRetriever->getImages());
        $this->assertEmpty($imagesRetriever->getImages());
    }

    public function testCanGetImagesFromAnAPI(): void
    {
        $imagesRetriever = new ImagesRetriever(
            new MockHttpClient([
                $response = new MockResponse(json_encode([
                    ['images' => ['https://i.imgur.com/a', 'b']],
                    ['images' => ['https://i.imgur.com/c', 'd']],
                    ['images' => ['e', 'https://i.imgur.com/f']],
                ])),
                $response,
            ])
        );

        $this->assertEquals($imagesRetriever->getImages(2), ['https://i.imgur.com/a', 'https://i.imgur.com/c']);
        $this->assertEquals($imagesRetriever->getImages(1), ['https://i.imgur.com/a']);
    }

    public function testCanGetRandomImages(): void
    {
        $imagesRetriever = new ImagesRetriever(new MockHttpClient());

        $this->assertNull($imagesRetriever->random([]));
        $this->assertContains($imagesRetriever->random($expected = [1, 2, 3]), $expected);
    }
}
