<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Entity\AbstractFile;

/**
 * Class File
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class File extends AbstractFile
{
    /**
     * @var \DateTime
     */
    private $createdAt;


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
