<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class BlockView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BlockView
{
    /**
     * @var AttributesInterface
     */
    private $attributes;

    /**
     * @var AttributesInterface
     */
    private $pluginAttributes;

    /**
     * @var string
     */
    public $content = '';


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = new Attributes();
        $this->pluginAttributes = new Attributes();
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

    /**
     * Returns the plugin attributes.
     *
     * @return AttributesInterface
     */
    public function getPluginAttributes()
    {
        return $this->pluginAttributes;
    }
}
