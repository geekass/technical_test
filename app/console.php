#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Scraper\Command\ScrapCommand;

$application = new Application('Sainsbury', '1.0.0');
$application->add(new ScrapCommand());
$application->run();