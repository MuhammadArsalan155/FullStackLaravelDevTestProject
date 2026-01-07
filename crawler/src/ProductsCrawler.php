<?php

namespace FullstackLaravelDevTest\Crawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ProductsCrawler
{
    private Client $client;
    private string $baseUrl = 'https://sandbox.oxylabs.io/products';

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 15,
            'headers' => [
                'User-Agent' => 'FullstackCrawler/1.0'
            ],
        ]);
    }

    public function crawl(): array
    {
        $html = (string) $this->client->get($this->baseUrl)->getBody();
        $crawler = new Crawler($html);
        $products = [];

        foreach ($crawler->filter('div.product-card') as $card) {
            $node = new Crawler($card);

            // Title
            $title = $node->filter('h4.title')->count() ? trim($node->filter('h4.title')->text()) : null;

            // Description
            $description = $node->filter('p.description')->count() ? trim($node->filter('p.description')->text()) : null;

            // Category (multiple spans)
            $category = null;
            if ($node->filter('p.category span')->count()) {
                $category = $node->filter('p.category span')->each(fn($span) => trim($span->text()));
                $category = implode(', ', $category);
            }

            // Price (replace comma with dot for float)
            $price = null;
            if ($node->filter('div.price-wrapper')->count()) {
                $rawPrice = $node->filter('div.price-wrapper')->text();
                $price = floatval(str_replace([',', '€', '$'], ['.', '', ''], $rawPrice));
            }

            // Image URL (relative → absolute)
            $image = null;
            if ($node->filter('img')->count()) {
                $src = $node->filter('img')->attr('src');
                $image = rtrim($this->baseUrl, '/') . '/' . ltrim($src, '/'); // full URL
            }

            $products[] = [
                'title'       => $title,
                'description' => $description,
                'category'    => $category,
                'price'       => $price,
                'image'       => $image
            ];
        }

        return $products;
    }
}
