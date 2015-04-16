<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Class GallerySubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GallerySubjectInterface
{
    /**
     * Sets the gallery.
     *
     * @param GalleryInterface $gallery
     * @return GallerySubjectInterface|$this
     */
    public function setGallery(GalleryInterface $gallery);

    /**
     * Returns the gallery.
     *
     * @return GalleryInterface
     */
    public function getGallery();
}
