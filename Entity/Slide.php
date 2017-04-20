<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Model\SlideInterface;
use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Slide
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method SlideTranslation translate($locale = null, $create = false)
 * @method Collection|SlideTranslation[] getTranslations()
 */
class Slide extends RM\AbstractTranslatable implements SlideInterface
{
    use RM\SortableTrait;

    private ?int       $id        = null;
    private ?string    $name      = null;
    private ?SlideShow $slideShow = null;
    private ?string    $type      = null;
    private array      $data      = [];


    /**
     * Returns the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?: 'New slide';
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
    public function setName(string $name): SlideInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSlideShow(): ?SlideShowInterface
    {
        return $this->slideShow;
    }

    /**
     * @inheritDoc
     */
    public function setSlideShow(SlideShowInterface $slideShow = null): SlideInterface
    {
        $this->slideShow = $slideShow;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type): SlideInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data): SlideInterface
    {
        $this->data = $data;

        return $this;
    }
}
