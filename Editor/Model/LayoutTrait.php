<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Trait LayoutTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait LayoutTrait
{
    /**
     * @var array
     */
    protected $layout = [];


    /**
     * Sets the layout.
     *
     * @param array $layout
     */
    public function setLayout(array $layout)
    {
        $this->layout = $layout;
    }

    /**
     * Returns the layout.
     *
     * @return array
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
