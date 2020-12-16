<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Model as RM;

/**
 * Class Slider
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method SlideTranslation translate($locale = null, $create = false)
 */
class Slide extends RM\AbstractTranslatable implements RM\SortableInterface, RM\ResourceInterface
{
    use RM\SortableTrait;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var SlideShow
     */
    private $slideShow;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $data = [];


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
     * Returns the id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return Slide
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the slide show.
     *
     * @return SlideShow
     */
    public function getSlideShow()
    {
        return $this->slideShow;
    }

    /**
     * Sets the slide show.
     *
     * @param SlideShow $slideShow
     *
     * @return Slide
     */
    public function setSlideShow(SlideShow $slideShow)
    {
        $this->slideShow = $slideShow;

        return $this;
    }

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return Slide
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the data.
     *
     * @param array $data
     *
     * @return Slide
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
