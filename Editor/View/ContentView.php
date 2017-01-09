<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class ContentView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentView
{
    /**
     * @var AttributesInterface
     */
    private $attributes;

    /**
     * @var array|ContainerView[]
     */
    public $containers = [];


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = new Attributes();
    }

    /**
     * Returns the attributes.
     *
     * @return AttributesInterface
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
