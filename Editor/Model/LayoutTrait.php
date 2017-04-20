<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Trait LayoutTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait LayoutTrait
{
    protected array $layout = [];


    /**
     * Sets the layout.
     *
     * @param array $layout
     */
    public function setLayout(array $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * Returns the layout.
     *
     * @return array
     */
    public function getLayout(): array
    {
        return $this->layout;
    }
}
