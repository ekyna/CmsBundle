<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model\SlideInterface;
use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;

/**
 * Class SlideShow
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShow implements SlideShowInterface
{
    private ?int $id = null;
    private ?string $name  = null;
    private ?string $tag = null;
    private Collection $slides;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->slides = new ArrayCollection();
    }

    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New slide show';
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): SlideShowInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @inheritDoc
     */
    public function setTag(string $tag = null): SlideShowInterface
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addSlide(SlideInterface $slide): SlideShowInterface
    {
        if (!$this->slides->contains($slide)) {
            $this->slides->add($slide);
            $slide->setSlideShow($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeSlide(SlideInterface $slide): SlideShowInterface
    {
        if ($this->slides->contains($slide)) {
            $this->slides->removeElement($slide);
            $slide->setSlideShow(null);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSlides(): Collection
    {
        return $this->slides;
    }
}
