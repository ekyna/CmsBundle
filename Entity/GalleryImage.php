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
        Core\SortableTrait,
        Core\TaggedEntityTrait;

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
     * {@inheritdoc}
     */
    public function setGallery(Cms\GalleryInterface $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->image->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->image->getAlt();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTags()
    {
        $tags = [$this->getEntityTag()];
        if (null !== $this->image) {
            $tags[] = $this->image->getEntityTag();
        }
        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.gallery';
    }
}
