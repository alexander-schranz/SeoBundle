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
    private $depth = 0;

    /**
     * @var bool
     */
    private $external;

    /**
     * @var int
     */
    private $externalUrls = 0;

    /**
     * @var int
     */
    private $internalUrls = 0;

    /**
     * @var bool
     */
    private $finished = false;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var array
     */
    private $clientOptions;

    /**
     * @var Collection|Url[]
     */
    private $urls;

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
        $this->created = new \DateTime();
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
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return $this;
     */
    public function increaseExternalUrls()
    {
        ++$this->externalUrls;

        return $this;
    }

    /**
     * @return $this;
     */
    public function increaseInternalUrls()
    {
        ++$this->internalUrls;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this;
     */
    public function increaseByType($type)
    {
        $type === Url::TYPE_INTERNAL ? ++$this->internalUrls : ++$this->externalUrls;

        return $this;
    }

    /**
     * @return int
     */
    public function getExternalUrls()
    {
        return $this->externalUrls;
    }

    /**
     * @return int
     */
    public function getInternalUrls()
    {
        return $this->internalUrls;
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
        $this->increaseByType($url->getType());

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
