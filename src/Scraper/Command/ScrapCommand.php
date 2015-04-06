<?php

namespace Scraper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Scraper\Crawler\CrawlGrocery;

class ScrapCommand extends Command
{
    protected function configure()
    {
        $this->setName('scrape');
        $this->setDescription("Scrap sainsbury's website");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $crawler = new CrawlGrocery();
        $result = $crawler->getOutput();
        $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
