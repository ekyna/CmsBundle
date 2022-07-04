<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\TranslatableInterface;
use Ekyna\Component\Resource\Model\TranslatableTrait;
use UnexpectedValueException;

/**
 * Class Tabs
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @method TabsTranslation translate($locale = null, $create = false)
 * @method Collection|TabsTranslation[] getTranslations()
 */
class Tabs implements TranslatableInterface
{
    use TranslatableTrait;

    private string $theme = 'default';
    private string $align = 'left';
    private Collection $tabs;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initializeTranslations();

        $this->tabs = new ArrayCollection();
    }

    /**
     * @inheritDoc
     *
     * @TODO Remove
     */
    public function getId(): ?int
    {
        return null;
    }

    /**
     * Returns the theme.
     *
     * @return string
     */
    public function getTheme(): string
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
    public function setTheme(string $theme): Tabs
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Returns the align.
     *
     * @return string
     */
    public function getAlign(): string
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
    public function setAlign(string $align): Tabs
    {
        $this->align = $align;

        return $this;
    }

    /**
     * Sets the tabs.
     *
     * @param array|Collection $tabs
     *
     * @return Tabs
     */
    public function setsTabs(Collection|array $tabs): Tabs
    {
        if (is_array($tabs)) {
            $tabs = new ArrayCollection($tabs);
        }

        if (!$tabs instanceof Collection) {
            throw new UnexpectedValueException('Expected array or instance of ' . ArrayCollection::class);
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
    public function addTab(Tab $tab): Tabs
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
    public function removeTab(Tab $tab): Tabs
    {
        if ($this->tabs->contains($tab)) {
            $this->tabs->removeElement($tab);
        }

        return $this;
    }

    /**
     * Returns the tabs.
     *
     * @return Collection|Tab[]
     */
    public function getTabs(): Collection
    {
        return $this->tabs;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->translate()->getTitle();
    }

    /**
     * @return MediaInterface|null
     */
    public function getMedia(): ?MediaInterface
    {
        return $this->translate()->getMedia();
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->translate()->getContent();
    }

    /**
     * @return string|null
     */
    public function getButtonLabel(): ?string
    {
        return $this->translate()->getButtonLabel();
    }

    /**
     * @return string|null
     */
    public function getButtonUrl(): ?string
    {
        return $this->translate()->getButtonUrl();
    }

    /**
     * Returns whether the tabs needs a button.
     *
     * @return bool
     */
    public function hasButton(): bool
    {
        if (!empty($this->getButtonLabel())) {
            return true;
        }

        foreach ($this->tabs as $tab) {
            if (!empty($tab->getButtonLabel())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether the tabs
     *
     * @return bool
     */
    public function isAnchorMode(): bool
    {
        foreach ($this->tabs as $tab) {
            if (!empty($tab->getAnchor())) {
                return true;
            }
        }

        return false;
    }
}
