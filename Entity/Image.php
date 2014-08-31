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
    public function __toString()
    {
        return pathinfo($this->getPath(), PATHINFO_BASENAME);
    }

    /**
     * @var \DateTime
     */
    private $deletedAt;

    /**
     * Sets the deletedAt datetime.
     *
     * @param \DateTime $deletedAt
     *
     * @return Image
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Returns the deletedAt datetime.
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
