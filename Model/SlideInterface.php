<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\SlideTranslation;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class SlideInterface
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method SlideTranslation translate($locale = null, $create = false)
 */
interface SlideInterface extends RM\TranslatableInterface, RM\SortableInterface
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
     * @return SlideInterface
     */
    public function setName(string $name): SlideInterface;

    /**
     * Returns the slide show.
     *
     * @return SlideShowInterface
     */
    public function getSlideShow(): ?SlideShowInterface;

    /**
     * Sets the slide show.
     *
     * @param SlideShowInterface|null $slideShow
     *
     * @return SlideInterface
     */
    public function setSlideShow(SlideShowInterface $slideShow = null): SlideInterface;

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType(): ?string;

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return SlideInterface
     */
    public function setType(string $type): SlideInterface;

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Sets the data.
     *
     * @param array $data
     *
     * @return SlideInterface
     */
    public function setData(array $data): SlideInterface;
}
