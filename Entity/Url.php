<?php

namespace L91\Bundle\SeoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Url
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';

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
     * @var Url
     */
    private $parent;

    /**
     * @var Collection|Url[]
     */
    private $children;

    /**
     * @param string $uri
     */
    public function __construct($uri, $depth = 0)
    {
        $this->uri = $uri;
        $this->depth = $depth;
        $this->children = new ArrayCollection();
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
     * @return Collection|Url[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}
