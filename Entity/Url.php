<?php

namespace L91\Bundle\SeoBundle\Entity;

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
     * @var int
     */
    private $depth = 0;

    /**
     * @var bool
     */
    private $timeout = false;

    /**
     * Url constructor.
     *
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Get uri.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set status code.
     *
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get depth.
     *
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set depth.
     *
     * @param int $depth
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
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
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}
