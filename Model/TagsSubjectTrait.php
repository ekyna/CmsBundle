<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Trait TagsSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait TagsSubjectTrait
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|TagInterface[]
     */
    protected $tags;


    /**
     * Adds the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function addTag(TagInterface $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Removes the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function removeTag(TagInterface $tag)
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * Returns the tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|TagInterface[]
     */
    public function getTags()
    {
        return $this->tags;
    }
}
