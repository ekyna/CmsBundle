<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ContentSubjectInterface
 */
interface ContentSubjectInterface
{
    /**
     * Sets the contents
     * 
     * @param ArrayCollection $contents
     * 
     * @return ContentSubjectInterface
     */
    public function setContents(ArrayCollection $contents);

    /**
     * Adds a content
     * 
     * @param Content $content
     * 
     * @return ContentSubjectInterface
     */
    public function addContent(Content $content);

    /**
     * Remove a content
     * 
     * @param Content $content
     * 
     * @return ContentSubjectInterface
     */
    public function removeContent(Content $content);

    /**
     * Returns all contents
     */
    public function getContents();

    /**
     * Returns the current content (last version)
     * 
     * @return Content
     */
    public function getContent();
}
