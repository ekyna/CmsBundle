<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\File;

/**
 * Trait FileSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait FileSubjectTrait
{
    /**
     * @var File
     */
    protected $file;

    /**
     * Returns the file.
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the file.
     *
     * @param File $file
     * @return FileSubjectInterface|$this
     */
    public function setFile(File $file = null)
    {
        $this->file = $file;

        return $this;
    }
}
