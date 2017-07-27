<?php

namespace L91\Bundle\SeoBundle\Crawler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class Crawler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array
     */
    private $options;

    /**
     * Crawler constructor.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->logger = new NullLogger();
    }

    /**
     * Crawl a specific uri.
     *
     * @param string $uri
     * @param integer $maxDepth
     * @param bool $external
     */
    public function crawl($uri, $maxDepth = 0, $external = false)
    {
        $urlTreeCrawler = new UrlTreeCrawler($uri, $maxDepth, $this->options, $external);
        $urlTreeCrawler->setLogger($this->logger);

        foreach ($urlTreeCrawler->crawl() as $url) {
            var_dump($url);
        }
    }
}
