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
            $products = $this->client
                ->request('GET', 'https://api.escuelajs.co/api/v1/products')
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }

        $images = [];

        foreach ($products as $product) {
            $images = array_merge($images, $product['images']);
        }

        $images = array_values(array_map(function ($result) {
            return (new UnicodeString($result))
                ->replace('[', '')
                ->replace(']', '')
                ->replace('"', '')
                ->toString();
        }, array_slice($images, 0, $number)));

        return empty($images) ? [] : $images;
    }

    public function random(array $images): mixed
    {
        return ! empty($images) ? $images[array_rand($images)] : null;
    }
}
