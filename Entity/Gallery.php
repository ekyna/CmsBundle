<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\GalleryImageInterface;
use Ekyna\Bundle\CmsBundle\Model\GalleryInterface;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Gallery
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Gallery implements GalleryInterface
{
    use Core\TimestampableTrait;
    use Core\TaggedEntityTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection|GalleryImageInterface[]
     */
    private $images;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setImages(ArrayCollection $images)
    {
        foreach ($images as $image) {
            $image->setGallery($this);
        }
        $this->images = $images;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasImage(GalleryImageInterface $image)
    {
        return $this->images->contains($image);
    }

    /**
     * {@inheritdoc}
     */
    public function addImage(GalleryImageInterface $image)
    {
        if (!$this->hasImage($image)) {
            $image->setGallery($this);
            $this->images->add($image);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeImage(GalleryImageInterface $image)
    {
        if ($this->hasImage($image)) {
            $image->setGallery(null);
            $this->images->removeElement($image);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTags()
    {
        $tags = [$this->getEntityTag()];
        foreach ($this->images as $image) {
            $tags = array_merge($tags, $image->getEntityTags());
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
