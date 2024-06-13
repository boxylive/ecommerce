<?php

namespace App;

use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ImagesRetriever
{
    public function __construct(private HttpClientInterface $client) {}

    public function getImages(int $number = 20): array
    {
        try {
            $response = $this->client
                ->request('GET', 'https://api.escuelajs.co/api/v1/products')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }

        $products = array_slice($response, 0, $number);
        $images = [];

        foreach ($products as $product) {
            $images = array_merge($images, $product['images']);
        }

        $images = array_values(array_filter($images, function ($result) {
            return (new UnicodeString($result))->startsWith('https://i.imgur.com');
        }));

        return empty($images) ? [] : $images;
    }

    public function random(array $images): mixed
    {
        return ! empty($images) ? $images[array_rand($images)] : null;
    }
}
