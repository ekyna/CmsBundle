<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class GalleryImageInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryImageInterface extends ImageSubjectInterface, Core\SortableInterface, Core\TaggedEntityInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Returns the gallery.
     *
     * @return GalleryInterface
     */
    public function getGallery();

    /**
     * Sets the gallery.
     *
     * @param GalleryInterface $gallery
     * @return GalleryImageInterface|$this
     */
    public function setGallery(GalleryInterface $gallery = null);

    /**
     * Image path getter alias.
     *
     * @return string
     */
    public function getPath();

    /**
     * Image alt getter alias.
     *
     * @return string
     */
    public function getAlt();
}
