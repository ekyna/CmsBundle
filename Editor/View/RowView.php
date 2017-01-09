<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class RowView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowView
{
    /**
     * @var AttributesInterface
     */
    private $attributes = [];

    /**
     * @var array|BlockView[]
     */
    public $blocks = [];


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
