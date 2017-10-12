<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\Entity\SlideShow;

/**
 * Interface RendererInterface
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RendererInterface
{
    /**
     * Returns the registry.
     *
     * @return TypeRegistryInterface
     */
    public function getRegistry();

    /**
     * Renders the slide show.
     *
     * @param SlideShow $slideShow
     * @param array     $options
     *
     * @return string
     */
    public function render(SlideShow $slideShow, array $options = []);
}
