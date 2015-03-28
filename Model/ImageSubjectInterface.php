<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Image;

/**
 * Interface ImageSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ImageSubjectInterface
{
    /**
     * Returns the image.
     *
     * @return mixed
     */
    public function getImage();

    /**
     * Sets the image.
     *
     * @param Image $image
     * @return ImageSubjectInterface|$this
     */
    public function setImage(Image $image = null);
}
