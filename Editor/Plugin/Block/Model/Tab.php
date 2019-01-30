<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Ekyna\Component\Resource\Model\SortableInterface;
use Ekyna\Component\Resource\Model\SortableTrait;
use Ekyna\Component\Resource\Model\TranslatableInterface;
use Ekyna\Component\Resource\Model\TranslatableTrait;

/**
 * Class Tab
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method TabTranslation translate($locale = null, $create = false)
 * @method TabTranslation[] getTranslations()
 */
class Tab implements TranslatableInterface, SortableInterface
{
    use TranslatableTrait,
        SortableTrait;

    /**
     * @var string
     */
    private $anchor;


    /**
     * @inheritDoc
     */
    public function getId()
    {
        return null;
    }

    /**
     * Returns the anchor.
     *
     * @return string
     */
    public function getAnchor()
    {
        return $this->anchor;
    }

    /**
     * Sets the anchor.
     *
     * @param string $anchor
     *
     * @return Tab
     */
    public function setAnchor(string $anchor = null)
    {
        $this->anchor = $anchor;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @return \Ekyna\Bundle\MediaBundle\Model\MediaInterface
     */
    public function getMedia()
    {
        return $this->translate()->getMedia();
    }

    /**
     * @return string
     */
    public function getButtonLabel()
    {
        return $this->translate()->getButtonLabel();
    }

    /**
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->translate()->getButtonUrl();
    }
}
