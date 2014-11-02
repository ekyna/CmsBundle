<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface TagSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface TagSubjectInterface
{
    /**
     * Sets the tags.
     *
     * @param ArrayCollection $tags
     * @return TagSubjectInterface|$this
     */
    public function setTags(ArrayCollection $tags);

    /**
     * Returns whether the subject has the given tag or not.
     *
     * @param TagInterface $tag
     * @return bool
     */
    public function hasTag(TagInterface $tag);

    /**
     * Adds the tag.
     *
     * @param TagInterface $tag
     * @return TagSubjectInterface|$this
     */
    public function addTag(TagInterface $tag);

    /**
     * Removes the tag.
     *
     * @param TagInterface $tag
     * @return TagSubjectInterface|$this
     */
    public function removeTag(TagInterface $tag);

    /**
     * Returns the tags.
     *
     * @return ArrayCollection|TagInterface[]
     */
    public function getTags();
}
