<?php
use Symfony\Component\DomCrawler\Crawler;
use Scraper\Crawler\CrawlGrocery;

class CrawlGroceryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var CrawlGrocery
     */
    private $extractor;

    public function setUp()
    {
        $this->crawler = new Crawler(file_get_contents(__DIR__ . '/../Seed/main.html', FILE_USE_INCLUDE_PATH));
        $this->extractor = new CrawlGrocery();
    }

    public function test_extract_url()
    {
        $link_name = "Sainsbury's Mango, Ripe & Ready x2";
        $actual = $this->extractor->extractHref($this->crawler, $link_name);
        $expected = "http://www.sainsburys.co.uk/shop/gb/groceries/ripe---ready/sainsburys-mango--ripe---ready-x2";
        $this->assertEquals($expected, $actual);
    }

    public function test_extract_unit_price()
    {
        $actual = $this->extractor->extractUnitPrice($this->crawler);
        $this->assertEquals(1.50, $actual);
    }

    public function test_extract_title_from_link()
    {
        $actual = $this->extractor->extractTitleFromLink($this->crawler->filter('.product'));
        $this->assertEquals("Sainsbury's Avocado Ripe & Ready XL Loose 300g", $actual);
    }

    public function test_extract_desc()
    {
        $crawler = new Crawler(file_get_contents(__DIR__ . '/../Seed/desc.html', FILE_USE_INCLUDE_PATH));
        $actual = $this->extractor->extractDesc($crawler);
        $this->assertEquals("by Sainsbury's Ripe and Ready Mango", $actual);
    }
}