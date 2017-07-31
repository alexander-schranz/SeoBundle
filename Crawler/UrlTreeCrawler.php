<?php

namespace L91\Bundle\SeoBundle\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use L91\Bundle\SeoBundle\Entity\Crawl;
use L91\Bundle\SeoBundle\Entity\Link;
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
     * @var Crawl
     */
    private $crawl;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $queueTree = [];

    /**
     * @var int
     */
    private $currentDepth = 0;

    /**
     * @var int
     */
    private $currentPosition = 0;

    /**
     * @var Url[]
     */
    private $urlList = [];

    /**
     * @var Url[]
     */
    private $linkList = [];

    /**
     * UrlTreeCrawler constructor.
     *
     * @param Crawl $crawl
     */
    public function __construct(Crawl $crawl)
    {
        $this->crawl = $crawl;
        $this->client = new Client($crawl->getClientOptions());
        $this->addUrl($crawl->getUri());
        $this->logger = new NullLogger();
    }

    /**
     * Crawl url tree.
     *
     * @return Url[]
     */
    public function run()
    {
        $this->urlList = [];

        /** @var Url[] $uriList */
        while ($urlList = array_pop($this->queueTree)) {

            /** @var Url $url */
            while($url = array_pop($urlList)) {
                $this->logger->info(sprintf('Crawl url "%s" at depth "%s"', $url->getUri(), $this->currentDepth));
                yield $this->crawlUrl($url);
            }

            ++$this->currentDepth;
            if ($this->currentDepth > $this->crawl->getDepth()) {
                $this->logger->info(sprintf('Max depth "%s" reached', $this->crawl->getDepth()));

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
            if (!$e->hasResponse()) {
                $url->setTimeout(false);
                $url->setStatusCode(0);

                return $url;
            }

            $response = $e->getResponse();
        }

        $statusCode = $response->getStatusCode();
        $url->setTimeout(false);
        $url->setStatusCode($statusCode);

        if ($url->getType() === Url::TYPE_EXTERNAL) {
            // Do not crawl content of external urls.
            return $url;
        }

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

            $this->logger->debug(sprintf('Found url "%s"', $link->getUri()));
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

        if ($parent && $parent->getUri() !== $url->getUri()) {
            $link = new Link($parent, $url);
            $key = $link->getKey();

            if (!isset($this->linkList[$key])) {
                $parent->addOutgoingLink($link);
                $url->addIncomingLink($link);
                $this->linkList[$key] = $link;
            }
        }

        // If was crawled do nothing.
        if ($url->getStatusCode() || $url->getTimeout()) {
            return;
        }

        // If external is not activate do not crawl external urls.
        if (!$this->crawl->getExternal() && $url->getType() === Url::TYPE_EXTERNAL) {
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
            ++$this->currentPosition;
            $url = new Url($this->crawl, $uri, $this->currentDepth + 1, $this->currentPosition);
            $url->setParent($parent);
            $url->setType($this->crawl->getTypeForUrl($url));
            $this->crawl->addUrl($url);

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
                'allow_redirects' => false,
            ]
        );
    }
}
