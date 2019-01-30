<?php

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

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var MediaInterface
     */
    private $media;

    /**
     * @var string
     */
    private $buttonLabel;

    /**
     * @var string
     */
    private $buttonUrl;


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
     * Returns the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return TabsTranslation
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent()
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
    public function setContent(string $content)
    {
        $this->content = $content;

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
     * @return TabsTranslation
     */
    public function setMedia(MediaInterface $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the buttonLabel.
     *
     * @return string
     */
    public function getButtonLabel()
    {
        return $this->buttonLabel;
    }

    /**
     * Sets the buttonLabel.
     *
     * @param string $label
     *
     * @return TabsTranslation
     */
    public function setButtonLabel(string $label = null)
    {
        $this->buttonLabel = $label;

        return $this;
    }

    /**
     * Returns the buttonUrl.
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->buttonUrl;
    }

    /**
     * Sets the buttonUrl.
     *
     * @param string $url
     *
     * @return TabsTranslation
     */
    public function setButtonUrl(string $url = null)
    {
        $this->buttonUrl = $url;

        return $this;
    }
}
