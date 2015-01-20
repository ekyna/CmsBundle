<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface TagsSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface TagsSubjectInterface
{
    /**
     * Sets the tags.
     *
     * @param ArrayCollection $tags
     * @return TagsSubjectInterface|$this
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
     * @return TagsSubjectInterface|$this
     */
    public function addTag(TagInterface $tag);

    /**
     * Removes the tag.
     *
     * @param TagInterface $tag
     * @return TagsSubjectInterface|$this
     */
    public function removeTag(TagInterface $tag);

    /**
     * Returns the tags.
     *
     * @return ArrayCollection|TagInterface[]
     */
    public function getTags();
}
