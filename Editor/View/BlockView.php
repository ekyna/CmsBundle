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
     * @var array|WidgetView[]
     */
    public $widgets = [];


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
