<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\SlideShow\DOMUtil;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Generator;

/**
 * Class AbstractType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractType implements TypeInterface
{
    /**
     * Returns the default theme choices.
     *
     * @return array
     */
    static public function getDefaultThemeChoices()
    {
        return [
            'light' => 'Light',
            'dark'  => 'Dark',
        ];
    }

    /**
     * @var MediaRepository
     */
    protected $mediaRepository;

    /**
     * @var Generator
     */
    protected $mediaGenerator;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $jsPath;

    /**
     * @var array
     */
    protected $config;


    /**
     * @inheritdoc
     */
    public function setMediaRepository(MediaRepository $repository)
    {
        $this->mediaRepository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function setMediaGenerator(Generator $generator)
    {
        $this->mediaGenerator = $generator;
    }

    /**
     * @inheritdoc
     */
    public function configure($name, $label, $jsPath, array $config = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->jsPath = $jsPath;
        $this->config = array_replace($this->getConfigDefaults(), $config);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function render(Slide $slide, \DOMElement $element, \DOMDocument $dom)
    {
        $data = $slide->getData();

        // Theme
        if (isset($data['theme']) && !empty($theme = $data['theme'])) {
            DOMUtil::addClass($element, 'cms-slide-' . $theme);
        }

        // Background color
        if (isset($data['background_color']) && !empty($color = $data['background_color'])) {
            DOMUtil::addStyle($element, 'background-color', $color);
        }

        // Background image
        $path = null;
        if (isset($data['background_id']) && 0 < $id = intval($data['background_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($id)) {
                $path = $this->mediaGenerator->generateFrontUrl($media, $this->config['background_filter']);

            }
        }
        if (null === $path && isset($data['background'])) {
            $path = $data['background'];
        }
        if (!empty($path)) {
            $background = $dom->createElement('div');
            $background->setAttribute('class', 'background');
            $background->setAttribute('style', "background-image:url('$path')");
            $element->appendChild($background);
        }
    }

    /**
     * Appends a wrapper to the element.
     *
     * @param \DOMElement  $element
     * @param \DOMDocument $dom
     *
     * @return \DOMElement
     */
    protected function appendWrapper(\DOMElement $element, \DOMDocument $dom)
    {
        $wrapper = $dom->createElement('div');
        $wrapper->setAttribute('class', 'cms-slide-wrapper');

        $element->appendChild($wrapper);

        return $wrapper;
    }

    /**
     * Returns the config defaults.
     *
     * @return array
     */
    protected function getConfigDefaults()
    {
        return [];
    }
}
