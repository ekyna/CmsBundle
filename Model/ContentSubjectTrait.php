<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Entity\Content;

/**
 * Class ContentSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    /**
     * @var ArrayCollection
     */
    protected $contents;

    /**
     * Returns the content (last version).
     *
     * @return Content|null
     */
    public function getContent()
    {
        if(null !== $this->contents && 0 < $this->contents->count()) {
            return $this->contents->first();
        }
        return null;
    }

    /**
     * Sets the contents.
     *
     * @param ArrayCollection $contents
     * @return ContentSubjectInterface|$this
     */
    public function setContents(ArrayCollection $contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Adds the content.
     *
     * @param Content $content
     * @return ContentSubjectInterface|$this
     */
    public function addContent(Content $content)
    {
        $this->contents->add($content);
    
        return $this;
    }

    /**
     * Removes the content.
     *
     * @param Content $content
     * @return ContentSubjectInterface|$this
     */
    public function removeContent(Content $content)
    {
        $this->contents->removeElement($content);

        return $this;
    }

    /**
     * Returns the contents.
     *
     * @return ArrayCollection|Content[]
     */
    public function getContents()
    {
        return $this->contents;
    }
}
