<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ContentSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait ContentSubjectTrait
{
    /**
     * @var ArrayCollection|ContentInterface[]
     */
    protected $contents;

    /**
     * Returns the content (last version).
     *
     * @return ContentInterface|null
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
     *
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
     * @param ContentInterface $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function addContent(ContentInterface $content)
    {
        $this->contents->add($content);
    
        return $this;
    }

    /**
     * Removes the content.
     *
     * @param ContentInterface $content
     *
     * @return ContentSubjectInterface|$this
     */
    public function removeContent(ContentInterface $content)
    {
        $this->contents->removeElement($content);

        return $this;
    }

    /**
     * Returns the contents.
     *
     * @return ArrayCollection|ContentInterface[]
     */
    public function getContents()
    {
        return $this->contents;
    }
}
