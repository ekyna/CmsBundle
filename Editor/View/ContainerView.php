<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class ContainerView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerView
{
    /**
     * @var AttributesInterface
     */
    private $attributes;

    /**
     * @var AttributesInterface
     */
    private $innerAttributes;

    /**
     * @var array|RowView[]
     */
    public $rows = [];

    /**
     * @var string
     */
    public $content = '';

    /**
     * @var string
     */
    public $innerContent = '';


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = new Attributes();
        $this->innerAttributes = new Attributes();
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
     * Returns the inner attributes.
     *
     * @return AttributesInterface
     */
    public function getInnerAttributes()
    {
        return $this->innerAttributes;
    }
}
