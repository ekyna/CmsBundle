<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * Interface RendererInterface
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RendererInterface extends RuntimeExtensionInterface
{
    /**
     * Returns the registry.
     *
     * @return TypeRegistryInterface
     */
    public function getRegistry(): TypeRegistryInterface;

    /**
     * Renders the slide show.
     *
     * @param SlideShow $slideShow
     * @param array     $options
     *
     * @return string
     */
    public function render(SlideShow $slideShow, array $options = []): string;
}
