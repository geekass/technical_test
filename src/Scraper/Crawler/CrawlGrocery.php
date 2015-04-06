<?php
namespace Scraper\Crawler;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class CrawlGrocery
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getOutput()
    {
        $crawler = new Crawler($this->getSource());
        $elements = $crawler->filter('.product');
        $results = [];

        $elements->each(function (Crawler $product) use (&$results) {
            $title = $this->extractTitleFromLink($product);
            $href = $this->extractHref($product, $title);
            $unit_price = $this->extractUnitPrice($product);
            $results[$href]['title'] = $title;
            $results[$href]['unit_price'] = $unit_price;
            $item_details = $this->client->get($href, ['cookies' => true]);
            $size = $item_details->getBody()->getSize();
            $results[$href]['size'] = $this->formatBytes($size, 2);
            $results[$href]['description'] = $this->extractDesc(new Crawler($item_details->getBody()->getContents()));

        });
        return array_values($results);
    }

    public function extractTitleFromLink(Crawler $product)
    {
        return trim($product->filter('.productInfo h3')->getNode(0)->nodeValue);
    }

    public function extractUnitPrice(Crawler $product)
    {
        preg_match('!\d+\.*\d*!', $product->filter('.pricePerUnit')->extract(['_text'])[0], $match);
        return $match[0];

    }

    public function extractHref(Crawler $crawler, $product_name)
    {
        return $crawler->selectLink($product_name)->getNode(0)->getAttribute('href');
    }

    public function extractDesc(Crawler $product)
    {
        $descriptions = $product->filter('.productText p')->extract(['_text']);
        return reset($descriptions) ?: null;
    }

    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('b', 'kb', 'mb', 'gb', 'tb');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . $units[$pow];
    }

    private function getSource()
    {
        $href = "http://www.sainsburys.co.uk/webapp/wcs/stores/servlet/CategoryDisplay?listView=true&orderBy=FAVOURITES_FIRST&parent_category_rn=12518&top_category=12518&langId=44&beginIndex=0&pageSize=20&catalogId=10137&searchTerm=&categoryId=185749&listId=&storeId=10151&promotionId=#langId=44&storeId=10151&catalogId=10137&categoryId=185749&parent_category_rn=12518&top_category=12518&pageSize=20&orderBy=FAVOURITES_FIRST&searchTerm=&beginIndex=0&hideFilters=true";
        return $this->client->get($href, ['cookies' => true])->getBody()->getContents();
    }
}
