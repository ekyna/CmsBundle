<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class GalleryImage
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GalleryImage implements Cms\GalleryImageInterface
{
    use Cms\ImageSubjectTrait,
        Core\SortableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Gallery
     */
    protected $gallery;


    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * Sets the gallery.
     *
     * @param Gallery $gallery
     * @return GalleryImage
     */
    public function setGallery(Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Image path getter alias.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->image->getPath();
    }

    /**
     * Image alt getter alias.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->image->getAlt();
    }
}
