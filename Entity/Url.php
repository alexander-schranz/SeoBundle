<?php

namespace L91\Bundle\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Url
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';

    /**
     * @var int
     */
    private $id;

    /**
     * @var Crawl
     */
    private $crawl;

    /**
     * @var string
     */
    private $uri;
    
    /**
     * @var int
     */
    private $statusCode = 0;

    /**
     * @var string
     */
    private $type = self::TYPE_INTERNAL;

    /**
     * @var bool
     */
    private $noIndex = false;

    /**
     * @var bool
     */
    private $noFollow = false;

    /**
     * @var bool
     */
    private $sitemap = false;

    /**
     * @var bool
     */
    private $timeout = false;

    /**
     * @var int
     */
    private $left;

    /**
     * @var int
     */
    private $right;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var int
     */
    private $position;

    /**
     * @var Url
     */
    private $parent;

    /**
     * @var Collection|Url[]
     */
    private $children;

    /**
     * @var Collection|Link[]
     */
    private $incomingLinks;

    /**
     * @var Collection|Link[]
     */
    private $outgoingLinks;

    /**
     * Url constructor.
     * @param Crawl $crawl
     * @param string $uri
     * @param int $depth
     * @param int $position
     */
    public function __construct(Crawl $crawl, $uri, $depth = 0, $position = 0)
    {
        $this->crawl = $crawl;
        $this->uri = $uri;
        $this->depth = $depth;
        $this->position = $position;
        $this->children = new ArrayCollection();
        $this->incomingLinks = new ArrayCollection();
        $this->outgoingLinks = new ArrayCollection();
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
     * @return Crawl
     */
    public function getCrawl()
    {
        return $this->crawl;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     *
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNoIndex()
    {
        return $this->noIndex;
    }

    /**
     * @param bool $noIndex
     *
     * @return $this
     */
    public function setNoIndex($noIndex)
    {
        $this->noIndex = $noIndex;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNoFollow()
    {
        return $this->noFollow;
    }

    /**
     * @param bool $noFollow
     *
     * @return $this
     */
    public function setNoFollow($noFollow)
    {
        $this->noFollow = $noFollow;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSitemap()
    {
        return $this->sitemap;
    }

    /**
     * @param bool $sitemap
     *
     * @return $this
     */
    public function setSitemap($sitemap)
    {
        $this->sitemap = $sitemap;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param bool $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return Url
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Url $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Url[]
     */
    public function getBreadcrumb()
    {
        return array_reverse($this->generateBreadcrumb($this));
    }

    /**
     * @param Url $url
     * @param array $list
     *
     * @return Url[]
     */
    protected function generateBreadcrumb($url, $list = [])
    {
        $list[] = $url;

        if ($url->getParent()) {
            $list = $this->generateBreadcrumb($url->getParent(), $list);
        }

        return $list;
    }

    /**
     * @return Collection|Url[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Collection|Link[]
     */
    public function getOutgoingLinks()
    {
        return $this->outgoingLinks;
    }

    /**
     * @param Link $link
     *
     * @return $this
     */
    public function addOutgoingLink(Link $link)
    {
        $this->outgoingLinks->add($link);

        return $this;
    }

    /**
     * @return Collection|Link[]
     */
    public function getIncomingLinks()
    {
        return $this->incomingLinks;
    }

    /**
     * @param Link $link
     *
     * @return $this
     */
    public function addIncomingLink(Link $link)
    {
        $this->incomingLinks->add($link);

        return $this;
    }
}
