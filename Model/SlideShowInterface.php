<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Resource\Model\ResourceInterface;

/**
 * Interface SlideShowInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface SlideShowInterface extends ResourceInterface
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): ?string;

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return SlideShowInterface
     */
    public function setName(string $name): SlideShowInterface;

    /**
     * Returns the tag.
     *
     * @return string
     */
    public function getTag(): ?string;

    /**
     * Sets the tag.
     *
     * @param string|null $tag
     *
     * @return SlideShowInterface
     */
    public function setTag(string $tag = null): SlideShowInterface;

    /**
     * Adds the slide.
     *
     * @param SlideInterface $slide
     *
     * @return SlideShowInterface
     */
    public function addSlide(SlideInterface $slide): SlideShowInterface;

    /**
     * Removes the slide.
     *
     * @param SlideInterface $slide
     *
     * @return SlideShowInterface
     */
    public function removeSlide(SlideInterface $slide): SlideShowInterface;

    /**
     * Returns the slides.
     *
     * @return Collection|SlideInterface[]
     */
    public function getSlides(): Collection;
}
