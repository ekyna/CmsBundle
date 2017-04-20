<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use DOMDocument;
use DOMElement;
use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\ImageBlockType;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImagePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ImagePlugin extends AbstractPlugin
{
    public const NAME = 'ekyna_block_image';

    private const DEFAULT_DATA = [
        'url'   => null,
        'image' => [
            'theme'     => null,
            'style'     => null,
            'align'     => null,
            'animation' => [
                'name'     => null,
                'offset'   => 120,
                'duration' => 400,
                'once'     => false,
            ],
            'max_width' => null,
        ],
        'hover' => [
            'theme'     => null,
            'style'     => null,
            'align'     => null,
            'animation' => [
                'name'     => null,
                'offset'   => 120,
                'duration' => 400,
                'once'     => false,
            ],
            'max_width' => null,
        ],
    ];

    private const DEFAULT_TRANSLATION_DATA = [
        'image' => [
            'media' => null,
        ],
        'hover' => [
            'media' => null,
        ],
    ];

    private MediaRepository $mediaRepository;
    private Generator $mediaGenerator;


    /**
     * Constructor.
     *
     * @param array           $config
     * @param MediaRepository $mediaRepository
     * @param Generator       $mediaGenerator
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        Generator $mediaGenerator
    ) {
        parent::__construct(array_replace([
            'default_path' => '/bundles/ekynacms/img/default-image.gif',
            'default_alt'  => 'Default image',
            'filter'       => 'cms_block_image',
            'themes'       => PropertyDefaults::getDefaultThemeChoices(),
            'styles'       => PropertyDefaults::getDefaultStyleChoices(),
            'animations'   => PropertyDefaults::getDefaultAnimationChoices(),
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->mediaGenerator = $mediaGenerator;
    }

    /**
     * @inheritDoc
     */
    public function create(BlockInterface $block, array $data = []): void
    {
        parent::create($block, $data);

        $block
            ->setData(array_merge(self::DEFAULT_DATA, $data))
            ->translate($this->localeProvider->getCurrentLocale())
            ->setData(self::DEFAULT_TRANSLATION_DATA);
    }

    /**
     * @inheritDoc
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response
    {
        $this->upgrade($block);

        $options = array_replace([
            'action'     => $this->urlGenerator->generate('admin_ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'     => 'post',
            'attr'       => [
                'class' => 'form-horizontal',
            ],
            'styles'     => $this->config['styles'],
            'themes'     => $this->config['themes'],
            'animations' => $this->config['animations'],
        ], $options);

        $form = $this->formFactory->create(ImageBlockType::class, $block, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModalResponse('Modifier le bloc image.', $form->createView());
    }

    /**
     * @inheritDoc
     */
    public function createWidget(
        BlockInterface $block,
        AdapterInterface $adapter,
        array $options,
        int $position = 0
    ): WidgetView {
        $this->upgrade($block);

        $data = array_replace_recursive(
            $block->getData(),
            $block->translate($this->localeProvider->getCurrentLocale())->getData()
        );

        $view = parent::createWidget($block, $adapter, $options, $position);
        $view->getAttributes()->addClass('cms-image');

        $options = array_replace($this->config, ['animation' => true], $options);

        // TODO Refactor XMl build

        $buildResponsiveImg = function (MediaInterface $media, DOMElement $img) use ($block, $adapter) {
            $map = $adapter->getImageResponsiveMap($block);

            $url = null;
            $srcSet = [];
            $sizes = [];

            foreach ($map as $filter => $config) {
                $url = $this->mediaGenerator->generateFrontUrl($media, $filter);
                $srcSet[] = $url . ' ' . $config['width'] . 'w';
                $sizes[] = trim(
                    ($config['max'] ? '(max-width:' . $config['max'] . 'px)' : '') . ' ' . $config['width'] . 'px'
                );
            }
            $img->setAttribute('src', $url);
            $img->setAttribute('srcset', implode(', ', $srcSet));
            $img->setAttribute('sizes', implode(', ', $sizes));
        };

        $dom = new DOMDocument();

        // Image
        $image = null;
        if (isset($data['image'])) {
            $imageData = $data['image'];
            $hasTheme = false;
            $classes = ['img-responsive'];
            if (isset($imageData['style']) && isset($this->config['styles'][$imageData['style']])) {
                $classes[] = $imageData['style'];
            }
            if (isset($imageData['theme']) && isset($this->config['themes'][$imageData['theme']])) {
                $classes[] = $imageData['theme'];
                $hasTheme = true;
            }
            if (isset($imageData['media']) && 0 < $mediaId = intval($imageData['media'])) {
                /** @var MediaInterface $imageMedia */
                if (null !== $imageMedia = $this->mediaRepository->find($mediaId)) {
                    if ($hasTheme && $imageMedia->getType() === MediaTypes::SVG) {
                        $import = new DOMDocument();
                        $import->loadXML($this->mediaGenerator->getContent($imageMedia),
                            LIBXML_NOBLANKS | LIBXML_NOERROR);
                        $image = $dom->importNode($import->documentElement, true);
                    }
                    if (!$image) {
                        $image = $dom->createElement('img');
                        if ($title = $imageMedia->getTitle()) {
                            $image->setAttribute('alt', $title);
                        }
                        if ($imageMedia->getType() === MediaTypes::SVG) {
                            $image->setAttribute('src',
                                $this->mediaGenerator->generateFrontUrl($imageMedia, $options['filter'])
                            );
                        } else {
                            $buildResponsiveImg($imageMedia, $image);
                        }
                    }
                }
            }
        }
        // Image attributes
        if (!$image) {
            $image = $dom->createElement('img');
            $image->setAttribute('src', $options['default_path']);
            $image->setAttribute('alt', $options['default_alt']);
        }
        if (!empty($classes)) {
            $image->setAttribute('class', implode(' ', $classes));
        }
        if (isset($imageData['max_width'])) {
            $image->setAttribute('style', 'max-width:' . $imageData['max_width']);
        }

        // Hover image
        $hover = null;
        if (isset($data['hover'])) {
            $hoverData = $data['hover'];
            if (isset($hoverData['media']) && 0 < $hoverId = intval($hoverData['media'])) {
                /** @var MediaInterface $hoverMedia */
                if (null !== $hoverMedia = $this->mediaRepository->find($hoverId)) {
                    $hasTheme = false;
                    $classes = ['img-responsive'];
                    if (isset($hoverData['style']) && isset($this->config['styles'][$hoverData['style']])) {
                        $classes[] = $hoverData['style'];
                    }
                    if (isset($hoverData['theme']) && isset($this->config['themes'][$hoverData['theme']])) {
                        $classes[] = $hoverData['theme'];
                        $hasTheme = true;
                    }

                    $hover = null;
                    if ($hasTheme && $hoverMedia->getType() === MediaTypes::SVG) {
                        $import = new DOMDocument();
                        $import->loadXML($this->mediaGenerator->getContent($hoverMedia),
                            LIBXML_NOBLANKS | LIBXML_NOERROR);
                        $hover = $dom->importNode($import->documentElement, true);
                    }
                    if (!$hover) {
                        $hover = $dom->createElement('img');
                        if ($title = $hoverMedia->getTitle()) {
                            $hover->setAttribute('alt', $title);
                        }
                        if ($imageMedia->getType() === MediaTypes::SVG) {
                            // Src and Alt
                            $hover->setAttribute('src',
                                $this->mediaGenerator->generateFrontUrl($hoverMedia, $options['filter'])
                            );
                        } else {
                            $buildResponsiveImg($hoverMedia, $hover);
                        }
                    }

                    if (!empty($classes)) {
                        $hover->setAttribute('class', implode(' ', $classes));
                    }
                    if (isset($hoverData['max_width'])) {
                        $hover->setAttribute('style', 'max-width:' . $hoverData['max_width']);
                    }
                }
            }
        }

        // Wrappers
        /** @var DOMElement $imageWrapper */
        /** @var DOMElement $hoverWrapper */
        $hoverWrapper = null;
        $url = isset($data['url']) && !empty($data['url']) ? $data['url'] : null;
        if ($hover) {
            $imageWrapper = $dom->createElement('div');
            if ($url) {
                $hoverWrapper = $dom->createElement('a');
                $hoverWrapper->setAttribute('href', $url);
            } else {
                $hoverWrapper = $dom->createElement('div');
            }
        } elseif ($url) {
            $imageWrapper = $dom->createElement('a');
            $imageWrapper->setAttribute('href', $url);
        } else {
            $imageWrapper = $dom->createElement('div');
        }

        // Image wrapper attributes
        if (isset($imageData) && isset($imageData['align'])) {
            $imageWrapper->setAttribute('style', 'text-align:' . $imageData['align']);
        }
        if ($options['animation'] && isset($imageData) && isset($imageData['animation'])) {
            $animData = $imageData['animation'];
            if (isset($animData['name']) && isset($this->config['animations'][$animData['name']])) {
                $imageWrapper->setAttribute('data-aos', $animData['name']);
                foreach (['duration', 'offset', 'once'] as $prop) {
                    if (isset($animData[$prop]) && $animData[$prop]) {
                        $imageWrapper->setAttribute('data-aos-' . $prop, (string)$animData[$prop]);
                    }
                }
            }
        }
        $dom->appendChild($imageWrapper);
        $imageWrapper->appendChild($image);

        // Hover wrapper attributes
        if ($hoverWrapper) {
            $hasAnimation = false;
            $classes = ['img-over'];
            if (isset($hoverData['align'])) {
                $hoverWrapper->setAttribute('style', 'text-align:' . $hoverData['align']);
            }
            if ($options['animation'] && isset($hoverData['animation'])) {
                $animData = $hoverData['animation'];
                if (isset($animData['name']) && isset($this->config['animations'][$animData['name']])) {
                    $hasAnimation = true;
                    $hoverWrapper->setAttribute('data-aos', $animData['name']);
                    foreach (['duration', 'offset', 'once'] as $prop) {
                        if (isset($animData[$prop]) && $animData[$prop]) {
                            $hoverWrapper->setAttribute('data-aos-' . $prop, (string)$animData[$prop]);
                        }
                    }
                }
            }
            if (!$hasAnimation) {
                $classes[] = 'img-hover';
            }
            if (!empty($classes)) {
                $hoverWrapper->setAttribute('class', implode(' ', $classes));
            }
            $dom->appendChild($hoverWrapper);
            $hoverWrapper->appendChild($hover);
        }

        $view->content = $dom->saveHTML();

        return $view;
    }

    /**
     * Changes the block and translation data to follow the 2019-03-11 changes (poster and video per translation).
     *
     * @param BlockInterface $block
     *
     * @deprecated
     * @TODO Remove
     */
    private function upgrade(BlockInterface $block): void
    {
        $data = array_replace(self::DEFAULT_DATA, $block->getData());

        $translation = $block->translate($this->localeProvider->getFallbackLocale());

        $translationData = array_replace(self::DEFAULT_TRANSLATION_DATA, $translation->getData());

        if (isset($data['url'])) {
            $translationData['url'] = $data['url'];
            unset($data['url']);
        }
        foreach (['image', 'hover'] as $key) {
            if (isset($data[$key]) && isset($data[$key]['media'])) {
                $translationData[$key]['media'] = $data[$key]['media'];
                unset($data[$key]['media']);
            }
        }

        $block->setData($data);
        $translation->setData($translationData);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Image';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/block/image';
    }
}
