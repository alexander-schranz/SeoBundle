<?php

namespace L91\Bundle\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Crawl
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var integer
     */
    private $depth;

    /**
     * @var bool
     */
    private $external;

    /**
     * @var array
     */
    private $clientOptions;

    /**
     * @var Collection|Url[]
     */
    private $urls;

    /**
     * @var bool
     */
    private $finished = false;

    /**
     * Crawl constructor.
     * @param string $uri
     * @param int $depth
     * @param bool $external
     * @param array $clientOptions
     */
    public function __construct($uri, $depth = 0, $external = false, array $clientOptions = [])
    {
        $this->uri = $uri;
        $this->depth = $depth;
        $this->external = $external;
        $this->clientOptions = $clientOptions;
        $this->urls = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return bool
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @return array
     */
    public function getClientOptions()
    {
        return $this->clientOptions;
    }

    /**
     * @return bool
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param bool $finished
     *
     * @return $this
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * @return Collection|Url[]
     */
    public function getUrls()
    {
        return $this->urls;
    }

    /**
     * @param Url $url
     *
     * @return $this
     */
    public function addUrl(Url $url)
    {
        $this->urls->add($url);

        return $this;
    }

    /**
     * Is external url.
     *
     * @param Url $url
     *
     * @return bool
     */
    public function getTypeForUrl(Url $url)
    {
        $currentHost = parse_url($this->uri)['host'];

        if (in_array(parse_url($url->getUri())['host'], [$currentHost, 'www.' . $currentHost])) {
            return Url::TYPE_INTERNAL;
        }

        return Url::TYPE_EXTERNAL;
    }
}
