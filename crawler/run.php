<?php

require __DIR__ . '/vendor/autoload.php';

use FullstackLaravelDevTest\Crawler\ProductsCrawler;

$crawler = new ProductsCrawler();
$products = $crawler->crawl();

file_put_contents(
    __DIR__ . '/products.json',
    json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

echo "Products extracted successfully.\n";
