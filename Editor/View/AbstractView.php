<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class AbstractView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractView
{
    private AttributesInterface $attributes;

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
    public function getAttributes(): AttributesInterface
    {
        return $this->attributes;
    }
}
