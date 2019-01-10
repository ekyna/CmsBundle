<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\TranslatableInterface;
use Ekyna\Component\Resource\Model\TranslatableTrait;

/**
 * Class Tabs
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method TabsTranslation translate($locale = null, $create = false)
 */
class Tabs implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $align;

    /**
     * @var MediaInterface
     */
    private $media;

    /**
     * @var ArrayCollection|Tab[]
     */
    private $tabs;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initializeTranslations();

        $this->tabs = new ArrayCollection();
    }

    /**
     * @inheritdoc
     *
     * @TODO Remove
     */
    public function getId()
    {
        return null;
    }

    /**
     * Returns the theme.
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Sets the theme.
     *
     * @param string $theme
     *
     * @return Tabs
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Returns the align.
     *
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * Sets the align.
     *
     * @param string $align
     *
     * @return Tabs
     */
    public function setAlign(string $align)
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the media.
     *
     * @param MediaInterface $media
     *
     * @return Tabs
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Sets the tabs.
     *
     * @param array|ArrayCollection $tabs
     *
     * @return $this
     */
    public function setsTabs($tabs)
    {
        if (is_array($tabs)) {
            $tabs = new ArrayCollection($tabs);
        }
        if (!$tabs instanceof ArrayCollection) {
            throw new \UnexpectedValueException("Expected array or instance of " . ArrayCollection::class);
        }

        $this->tabs = $tabs;

        return $this;
    }

    /**
     * Adds the tab.
     *
     * @param Tab $tab
     *
     * @return Tabs
     */
    public function addTab(Tab $tab)
    {
        if (!$this->tabs->contains($tab)) {
            $this->tabs->add($tab);
        }

        return $this;
    }

    /**
     * Adds the tab.
     *
     * @param Tab $tab
     *
     * @return Tabs
     */
    public function removeTab(Tab $tab)
    {
        if ($this->tabs->contains($tab)) {
            $this->tabs->remove($tab);
        }

        return $this;
    }

    /**
     * Returns the tabs.
     *
     * @return Tab[]
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->translate()->getTitle();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->translate()->getContent();
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
