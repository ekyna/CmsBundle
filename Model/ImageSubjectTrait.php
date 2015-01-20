<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Image;

/**
 * Trait ImageSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ImageSubjectTrait
{
    /**
     * @var Image
     */
    protected $image;

    /**
     * Returns the image.
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image.
     *
     * @param Image $image
     * @return ImageSubjectInterface|$this
     */
    public function setImage(Image $image)
    {
        $this->image = $image;

        return $this;
    }
}
