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
            echo 'URI:          ' . $url->getUri() . PHP_EOL;
            echo 'Type:         ' . $url->getType() . PHP_EOL;
            echo 'Depth:        ' . $url->getDepth() . PHP_EOL;
            echo 'StatusCode:   ' . $url->getStatusCode() . PHP_EOL;
            echo 'Timeout:      ' . ($url->getTimeout() ? 'true' : 'false') . PHP_EOL;

            echo PHP_EOL;
        }
    }
}
