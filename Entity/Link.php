<?php

namespace L91\Bundle\SeoBundle\Entity;

class Link
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Url
     */
    private $source;

    /**
     * @var Url
     */
    private $target;

    /**
     * Link constructor.
     * @param Url $source
     * @param Url $target
     */
    public function __construct(Url $source, Url $target)
    {
        $this->target = $target;
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Url
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Url
     */
    public function getTarget()
    {
        return $this->target;
    }
}
