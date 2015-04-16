<?php

namespace Ekyna\Bundle\CmsBundle\Model;


use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Interface GalleryInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface GalleryInterface extends Core\TimestampableInterface, Core\TaggedEntityInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Sets the name.
     *
     * @param string $name
     * @return GalleryInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the images.
     *
     * @param ArrayCollection|GalleryImageInterface[] $images
     * @return GalleryInterface|$this
     */
    public function setImages(ArrayCollection $images);

    /**
     * Returns whether the images contains the image or not.
     *
     * @param GalleryImageInterface $image
     * @return bool
     */
    public function hasImage(GalleryImageInterface $image);

    /**
     * Adds the image.
     *
     * @param GalleryImageInterface $image
     * @return GalleryInterface|$this
     */
    public function addImage(GalleryImageInterface $image);

    /**
     * Removes the image.
     *
     * @param GalleryImageInterface $image
     * @return GalleryInterface|$this
     */
    public function removeImage(GalleryImageInterface $image);

    /**
     * Returns the images.
     *
     * @return ArrayCollection|GalleryImageInterface[]
     */
    public function getImages();
}