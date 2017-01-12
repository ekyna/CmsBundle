<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class WidgetView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class WidgetView
{
    /**
     * @var AttributesInterface
     */
    private $attributes;

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
