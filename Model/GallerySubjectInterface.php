<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Gallery;

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
     * @param Gallery $gallery
     * @return GallerySubjectInterface|$this
     */
    public function setGallery(Gallery $gallery);
    /**
     * Returns the gallery.
     *
     * @return Gallery
     */
    public function getGallery();
}
