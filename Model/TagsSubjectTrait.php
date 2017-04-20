<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Trait TagsSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait TagsSubjectTrait
{
    /**
     * @var Collection|TagInterface[]
     */
    protected $tags;


    /**
     * Initializes the tags collection.
     */
    protected function initializeTags(): void
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Adds the tag.
     *
     * @param TagInterface $tag
     *
     * @return $this|TagsSubjectInterface
     */
    public function addTag(TagInterface $tag): TagsSubjectInterface
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
    public function removeTag(TagInterface $tag): TagsSubjectInterface
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * Returns the tags
     *
     * @return Collection|TagInterface[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }
}
