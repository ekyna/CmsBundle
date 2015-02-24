<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Gallery
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Gallery implements Core\TimestampableInterface, Core\TaggedEntityInterface
{
    use Core\TimestampableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var ArrayCollection
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
     * Returns the id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     * @return Gallery
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the images.
     *
     * @param ArrayCollection|GalleryImage[] $images
     * @return Gallery
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
     * Returns whether the images contains the image or not.
     *
     * @param GalleryImage $image
     * @return bool
     */
    public function hasImage(GalleryImage $image)
    {
        return $this->images->contains($image);
    }

    /**
     * Adds the image.
     *
     * @param GalleryImage $image
     * @return Gallery
     */
    public function addImage(GalleryImage $image)
    {
        if (!$this->hasImage($image)) {
            $image->setGallery($this);
            $this->images->add($image);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * Removes the image.
     *
     * @param GalleryImage $image
     * @return Gallery
     */
    public function removeImage(GalleryImage $image)
    {
        if ($this->hasImage($image)) {
            $image->setGallery(null);
            $this->images->removeElement($image);
            $this->setUpdatedAt(new \DateTime());
        }
        return $this;
    }

    /**
     * Returns the images.
     *
     * @return ArrayCollection|GalleryImage[]
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTag()
    {
        if (null === $this->getId()) {
            throw new \RuntimeException('Unable to generate entity tag, as the id property is undefined.');
        }
        return sprintf('ekyna_cms.gallery[id:%s]', $this->getId());
    }
}
