<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Entity\AbstractImage;

/**
 * Class Image
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Image extends AbstractImage
{
    /**
     * @var \DateTime
     */
    private $createdAt;


    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    /**
     * Returns the createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets the createdAt.
     *
     * @param \DateTime $createdAt
     * @return Image
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
