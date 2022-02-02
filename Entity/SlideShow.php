<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model\SlideInterface;
use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;
use Ekyna\Component\Resource\Model\AbstractResource;

/**
 * Class SlideShow
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShow extends AbstractResource implements SlideShowInterface
{
    private ?string    $name = null;
    private ?string    $tag  = null;
    private Collection $slides;

    public function __construct()
    {
        $this->slides = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?: 'New slide show';
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): SlideShowInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag = null): SlideShowInterface
    {
        $this->tag = $tag;

        return $this;
    }

    public function addSlide(SlideInterface $slide): SlideShowInterface
    {
        if (!$this->slides->contains($slide)) {
            $this->slides->add($slide);
            $slide->setSlideShow($this);
        }

        return $this;
    }

    public function removeSlide(SlideInterface $slide): SlideShowInterface
    {
        if ($this->slides->contains($slide)) {
            $this->slides->removeElement($slide);
            $slide->setSlideShow(null);
        }

        return $this;
    }

    public function getSlides(): Collection
    {
        return $this->slides;
    }
}
