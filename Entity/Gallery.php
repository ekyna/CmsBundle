<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Gallery
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Gallery implements Core\TimestampableInterface
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
     * @param ArrayCollection $images
     * @return Gallery
     */
    public function setImages(ArrayCollection $images)
    {
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
            $this->images->add($image);
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
            $this->images->removeElement($image);
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
}