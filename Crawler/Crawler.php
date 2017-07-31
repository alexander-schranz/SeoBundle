<?php

namespace L91\Bundle\SeoBundle\Crawler;

use Doctrine\ORM\EntityManagerInterface;
use L91\Bundle\SeoBundle\Entity\Crawl;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class Crawler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $options;

    /**
     * Crawler constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param array $options
     */
    public function __construct(EntityManagerInterface $entityManager, $options)
    {
        $this->entityManager = $entityManager;
        $this->options = $options;
        $this->logger = new NullLogger();
    }

    /**
     * Crawl a specific uri.
     *
     * @param string $uri
     * @param int $maxDepth
     * @param bool $external
     *
     * @return Crawl
     */
    public function crawl($uri, $maxDepth = 0, $external = false)
    {
        $crawl = new Crawl($uri, $maxDepth, $external, $this->options);
        $this->entityManager->persist($crawl);
        $this->entityManager->flush();

        $urlTreeCrawler = new UrlTreeCrawler($crawl);
        $urlTreeCrawler->setLogger($this->logger);

        foreach ($urlTreeCrawler->run() as $url) {
            $this->logger->info(PHP_EOL .
                'URI:          ' . $url->getUri() . PHP_EOL .
                'Type:         ' . $url->getType() . PHP_EOL .
                'Depth:        ' . $url->getDepth() . PHP_EOL .
                'StatusCode:   ' . $url->getStatusCode() . PHP_EOL .
                'Position:     ' . $url->getPosition() . '/' . $urlTreeCrawler->getFoundUrls() . PHP_EOL .
                'Timeout:      ' . ($url->getTimeout() ? 'true' : 'false') . PHP_EOL .
                PHP_EOL);

            $this->entityManager->persist($url);
            $this->entityManager->flush();
        }

        $crawl->setFinished(true);
        $this->entityManager->persist($crawl);
        $this->entityManager->flush();

        return $crawl;
    }
}
