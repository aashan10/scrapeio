# ScrapeIO
This library aims to simplify crawling the web for results.

## Installation
`composer require aashan10/scrapeio`

## Usage

The scraping API is provided by `Scrapeio\Scrapper` class and can be used in a pattern below.

```php
<?php
...
require_once 'vendor/autoload.php';
...

$scrapeio = new \Scrapeio\Scraper('GET', 'https://www.php.net/manual/en/function.preg-match.php');
$collection = $scrapeio
    ->setQuery('//div[@id="function.preg-match"]//p[@class="refpurpose"]')
    ->scrape();

echo ($collection->first()->escapeHtmlAndGetData());
echo "<br />";
$collection = $scrapeio->setQuery('//section[@id="usernotes"]//div[@id="allnotes"]//div//strong[@class="user"]//em')->scrape();
echo $collection->count() . ' users contributed to this documentation in php.net!';```