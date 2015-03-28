<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\File;

/**
 * Interface FileSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface FileSubjectInterface
{
    /**
     * Returns the file.
     *
     * @return mixed
     */
    public function getFile();

    /**
     * Sets the file.
     *
     * @param File $file
     * @return FileSubjectInterface|$this
     */
    public function setFile(File $file = null);
}
