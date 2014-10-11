<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ContentSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentSubjectInterface
{
    /**
     * Returns the current content (last version).
     * 
     * @return ContentInterface|null
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
     * @param ContentInterface $content
     * @return ContentSubjectInterface|$this
     */
    public function addContent(ContentInterface $content);

    /**
     * Remove the content
     * 
     * @param ContentInterface $content
     * @return ContentSubjectInterface|$this
     */
    public function removeContent(ContentInterface $content);

    /**
     * Returns all contents
     *
     * @return ArrayCollection|ContentInterface[]
     */
    public function getContents();
}
