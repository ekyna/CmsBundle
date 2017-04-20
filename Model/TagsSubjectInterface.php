<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;

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
    public function addTag(TagInterface $tag): TagsSubjectInterface;

    /**
     * Removes the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function removeTag(TagInterface $tag): TagsSubjectInterface;

    /**
     * Returns the tags
     *
     * @return Collection|TagInterface[]
     */
    public function getTags(): Collection;
}
