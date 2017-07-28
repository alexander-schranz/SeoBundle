<?php

namespace L91\Bundle\SeoBundle\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use L91\Bundle\SeoBundle\Entity\Url;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\DomCrawler\Crawler;

class UrlTreeCrawler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var integer
     */
    private $maxDepth;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var bool
     */
    private $external;

    /**
     * @var array
     */
    private $queueTree = [];

    /**
     * @var integer
     */
    private $currentDepth = 0;

    /**
     * @var Url[]
     */
    private $urlList = [];

    /**
     * UrlTreeCrawler constructor.
     *
     * @param string $uri
     * @param int $maxDepth
     * @param array $clientOptions
     * @param bool $external
     */
    public function __construct($uri, $maxDepth = 0, $clientOptions = [], $external = false)
    {
        $this->client = new Client($clientOptions);
        $this->uri = $uri;
        $this->addUrl($uri);
        $this->maxDepth = $maxDepth;
        $this->external = $external;
        $this->logger = new NullLogger();
    }

    /**
     * Crawl url tree.
     *
     * @return Url[]
     */
    public function crawl()
    {
        $this->urlList = [];

        /** @var Url[] $uriList */
        while ($urlList = array_pop($this->queueTree)) {
            ++$this->currentDepth;

            /** @var Url $url */
            while($url = array_pop($urlList)) {
                $this->logger->info(sprintf('Crawl url "%s" at depth "%s"', $url->getUri(), $this->currentDepth));
                yield $this->crawlUrl($url);
            }

            if ($this->currentDepth > $this->maxDepth) {
                $this->logger->info(sprintf('Max depth "%s" reached', $this->maxDepth));

                break;
            }
        }

        $this->logger->info(sprintf('Finished crawl with "%s" results', count($this->urlList)));
    }

    /**
     * Crawl url.
     *
     * @param Url $url
     *
     * @return Url
     */
    private function crawlUrl(Url $url)
    {
        try {
            $response = $this->request($url->getUri());
        } catch (ConnectException $e) {
            $url->setTimeout(true);
            $url->setStatusCode(0);

            return $url;
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        $statusCode = $response->getStatusCode();
        $url->setTimeout(false);
        $url->setStatusCode($statusCode);

        switch ($statusCode) {
            case 200:
            case 201:
            case 202:
            case 206:
                if ($url->getType() == Url::TYPE_INTERNAL) {
                    $this->analyseContent($response->getBody()->getContents(), $url);
                }

                break;

            case 301:
            case 302:
            case 303:
            case 307:
            case 308:
                foreach ($response->getHeader('Location') as $uri) {
                    $this->addUrl($uri, $url);
                }

                break;
        }

        return $url;
    }

    /**
     * Analyse content.
     *
     * @param string $content
     * @param Url $url
     */
    private function analyseContent($content, Url $url)
    {
        $crawler = new Crawler($content, $url->getUri());
        foreach ($crawler->filter('a, link, area')->links() as $link) {
            if ($link->getNode()->nodeName === 'link'
                && !in_array($link->getNode()->getAttribute('rel'), ['prev', 'next', 'canonical'])
            ) {
                continue;
            }

            $this->logger->info(sprintf('Found url "%s"', $link->getUri()));
            $this->addUrl($link->getUri(), $url);
        }
    }

    /**
     * Add url.
     *
     * @param string $uri
     * @param Url $parent
     */
    private function addUrl($uri, $parent = null)
    {
        if (!$this->isValidUri($uri)) {
            return;
        }

        $url = $this->getOrCreateUrl($uri, $parent);

        // If was crawled do nothing.
        if ($url->getStatusCode() || $url->getTimeout()) {
            return;
        }

        // If external is not activate do not crawl external urls.
        if (!$this->external && $url->getType() === Url::TYPE_EXTERNAL) {
            return;
        }

        // Else add url to crawl list.
        if (!isset($this->queueTree[$url->getDepth()])) {
            $this->queueTree[$url->getDepth()] = [];
        }

        array_push($this->queueTree[$url->getDepth()], $url);
    }

    /**
     * Get or create url.
     *
     * @param string $uri
     * @param Url $parent
     *
     * @return Url
     */
    private function getOrCreateUrl($uri, $parent = null)
    {
        // Split hash urls correctly.
        $uri = explode('#', $uri, 2)[0];

        if (!isset($this->urlList[$uri])) {
            $url = new Url($uri, $this->currentDepth);
            $url->setParent($parent);

            // Set type to external when external url.
            if ($this->isExternal($url)) {
                $url->setType(Url::TYPE_EXTERNAL);
            }

            $this->urlList[$uri] = $url;
        }

        return $this->urlList[$uri];
    }

    /**
     * Is valid uri.
     *
     * @param string $uri
     *
     * @return bool
     */
    private function isValidUri($uri)
    {
        $parts = parse_url($uri);

        return isset($parts['host']) && $parts['scheme'];
    }

    /**
     * Is external url.
     *
     * @param Url $url
     *
     * @return bool
     */
    private function isExternal(Url $url)
    {
        $currentHost = parse_url($this->uri)['host'];

        return !in_array(parse_url($url->getUri())['host'], [$currentHost, 'www.' . $currentHost]);
    }

    /**
     * Request a specific url.
     *
     * @param string $uri
     *
     * @return ResponseInterface
     */
    private function request($uri)
    {
        return $this->client->request(
            'GET',
            $uri,
            [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
                ],
                'allow_redirects' => false,
            ]
        );
    }
}
