<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\TranslationInterface;
use Ekyna\Component\Resource\Model\TranslationTrait;

/**
 * Class TabTranslation
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabTranslation implements TranslationInterface
{
    use TranslationTrait;

    private ?string         $title       = null;
    private ?MediaInterface $media       = null;
    private ?string         $buttonLabel = null;
    private ?string         $buttonUrl   = null;


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
     * Returns the title.
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string|null $title
     *
     * @return TabTranslation
     */
    public function setTitle(string $title = null): TabTranslation
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia(): ?MediaInterface
    {
        return $this->media;
    }

    /**
     * Sets the media.
     *
     * @param MediaInterface|null $media
     *
     * @return TabTranslation
     */
    public function setMedia(MediaInterface $media = null): TabTranslation
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the buttonLabel.
     *
     * @return string|null
     */
    public function getButtonLabel(): ?string
    {
        return $this->buttonLabel;
    }

    /**
     * Sets the buttonLabel.
     *
     * @param string|null $label
     *
     * @return TabTranslation
     */
    public function setButtonLabel(string $label = null): TabTranslation
    {
        $this->buttonLabel = $label;

        return $this;
    }

    /**
     * Returns the buttonUrl.
     *
     * @return string|null
     */
    public function getButtonUrl(): ?string
    {
        return $this->buttonUrl;
    }

    /**
     * Sets the buttonUrl.
     *
     * @param string|null $url
     *
     * @return TabTranslation
     */
    public function setButtonUrl(string $url = null): TabTranslation
    {
        $this->buttonUrl = $url;

        return $this;
    }
}
