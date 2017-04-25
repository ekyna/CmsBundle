<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface TagsSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface TagsSubjectInterface
{
    /**
     * Adds the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function addTag(TagInterface $tag);

    /**
     * Removes the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function removeTag(TagInterface $tag);

    /**
     * Returns the tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection|TagInterface[]
     */
    public function getTags();
}
