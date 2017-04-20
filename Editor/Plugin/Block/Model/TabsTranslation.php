<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model;

use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Component\Resource\Model\TranslationInterface;
use Ekyna\Component\Resource\Model\TranslationTrait;

/**
 * Class TabsTranslation
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsTranslation implements TranslationInterface
{
    use TranslationTrait;

    private ?string $title = null;
    private ?string $content = null;
    private ?MediaInterface $media = null;
    private ?string $buttonLabel = null;
    private ?string $buttonUrl = null;


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
     * @return TabsTranslation
     */
    public function setTitle(string $title = null): TabsTranslation
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Sets the content.
     *
     * @param string $content
     *
     * @return TabsTranslation
     */
    public function setContent(string $content): TabsTranslation
    {
        $this->content = $content;

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
     * @return TabsTranslation
     */
    public function setMedia(MediaInterface $media = null): TabsTranslation
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the buttonLabel.
     *
     * @return string
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
     * @return TabsTranslation
     */
    public function setButtonLabel(string $label = null): TabsTranslation
    {
        $this->buttonLabel = $label;

        return $this;
    }

    /**
     * Returns the buttonUrl.
     *
     * @return string
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
     * @return TabsTranslation
     */
    public function setButtonUrl(string $url = null): TabsTranslation
    {
        $this->buttonUrl = $url;

        return $this;
    }
}
