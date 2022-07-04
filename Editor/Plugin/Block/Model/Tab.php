<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\SortableInterface;
use Ekyna\Component\Resource\Model\SortableTrait;
use Ekyna\Component\Resource\Model\TranslatableInterface;
use Ekyna\Component\Resource\Model\TranslatableTrait;

/**
 * Class Tab
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 *
 * @template-extends TranslatableInterface<TabTranslation>
 */
class Tab implements TranslatableInterface, SortableInterface
{
    use TranslatableTrait;
    use SortableTrait;

    private ?string $anchor = null;

    public function getId(): ?int
    {
        return null;
    }

    /**
     * Returns the anchor.
     *
     * @return string|null
     */
    public function getAnchor(): ?string
    {
        return $this->anchor;
    }

    /**
     * Sets the anchor.
     *
     * @param string|null $anchor
     *
     * @return Tab
     */
    public function setAnchor(string $anchor = null): Tab
    {
        $this->anchor = $anchor;

        return $this;
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
}
