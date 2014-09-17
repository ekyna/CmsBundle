<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity\Content;

/**
 * ContentSubjectInterface.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentSubjectInterface
{
    /**
     * Returns the current content (last version).
     * 
     * @return Content
     */
    public function getContent();

    /**
     * Sets the contents.
     * 
     * @param ArrayCollection $contents
     * @return ContentSubjectInterface|$this
     */
    public function setContents(ArrayCollection $contents);

    /**
     * Adds the content.
     * 
     * @param Content $content
     * @return ContentSubjectInterface|$this
     */
    public function addContent(Content $content);

    /**
     * Remove the content
     * 
     * @param Content $content
     * @return ContentSubjectInterface|$this
     */
    public function removeContent(Content $content);

    /**
     * Returns all contents
     *
     * @return ArrayCollection|Content[]
     */
    public function getContents();
}
