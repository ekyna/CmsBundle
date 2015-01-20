<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Gallery;

/**
 * Trait GallerySubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait GallerySubjectTrait
{
    /**
     * @var Gallery
     */
    protected $gallery;

    /**
     * Sets the gallery.
     *
     * @param Gallery $gallery
     * @return GallerySubjectInterface|$this
     */
    public function setGallery(Gallery $gallery)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Returns the gallery.
     *
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}
