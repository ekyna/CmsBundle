<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\ImageBlockType;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ImagePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ImagePlugin extends AbstractPlugin
{
    const NAME = 'ekyna_block_image';


    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var Renderer
     */
    private $mediaRenderer;


    /**
     * Constructor.
     *
     * @param array           $config
     * @param MediaRepository $mediaRepository
     * @param Renderer        $mediaRenderer
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        Renderer $mediaRenderer
    ) {
        parent::__construct(array_replace([
            'default_path' => '/bundles/ekynacms/img/default-image.gif',
            'default_alt'  => 'Default image',
            'filter'       => 'cms_block_image',
            'styles'       => static::getDefaultStyleChoices(),
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->mediaRenderer = $mediaRenderer;
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        // TODO $defaultData
        //$defaultData = array_key_exists('default_data', $this->config) ? $this->config['default_data'] : array();

        $block->setData('media_id', null);
        $block->setData('hover_id', null);

        //$block->translate($this->localeProvider->getCurrentLocale(), true)->setData([]);
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        $options = array_replace([
            'repository'    => $this->mediaRepository,
            'action'        => $this->urlGenerator->generate('ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'        => 'post',
            'attr'          => [
                'class' => 'form-horizontal',
            ],
            'style_choices' => $this->config['styles'],
        ], $options);

        $form = $this->formFactory->create(ImageBlockType::class, $block->getData(), $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block->setData($form->getData());

            /*$data = $form->getData();

            $block->setData('media_id', $data['media_id']);
            $block->setData('media_id', $data['media_id']);
            $block->setData('style', $data['style']);
            $block->setData('url', $data['url']);*/

            return null;
        }

        return $this->createModal('Modifier le bloc image.', $form->createView());
    }

    /**
     * @inheritdoc
     */
    public function remove(BlockInterface $block)
    {
        parent::remove($block);
    }

    /**
     * @inheritdoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {
        /* TODO $data = $block->getData();

        if (!array_key_exists('media_id', $data)) {
            $context->addViolation(self::INVALID_DATA);
        }
        if (!array_key_exists('hover_id', $data)) {
            $context->addViolation(self::INVALID_DATA);
        }*/

        /*foreach ($block->getTranslations() as $blockTranslation) {
            $translationData = $blockTranslation->getData();

            if (0 < count($translationData)) {
                $context->addViolation(self::INVALID_DATA);
            }
        }*/
    }

    /**
     * @inheritDoc
     */
    public function createWidget(BlockInterface $block, array $options, $position = 0)
    {
        $view = parent::createWidget($block, $options, $position);

        $options = array_replace($this->config, $options);

        $data = $block->getData();
        $content = null;

        // Default content
        if (!$content) {
            $content = '<img ' .
                'src="' . $options['default_path'] . '" ' .
                'alt="' . $options['default_alt'] . '" ' .
                'class="img-responsive">';
        }

        // Image
        if (isset($data['media_id']) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                $content = $this->mediaRenderer->renderMedia($media, [
                    'filter' => $options['filter'],
                    'attr'   => [
                        'class' => 'img-responsive',
                    ],
                ]);
            }
        }

        // Hover image
        if (isset($data['hover_id']) && 0 < $hoverId = intval($data['hover_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $hover */
            if (null !== $hover = $this->mediaRepository->find($hoverId)) {
                $content .= $this->mediaRenderer->renderMedia($hover, [
                    'filter' => $options['filter'],
                    'attr'   => [
                        'class' => 'img-responsive img-hover',
                    ],
                ]);
            }
        }

        // Style
        $class = '';
        if (isset($data['style']) && isset($this->config['styles'][$data['style']])) {
            $class = ' class="' . $data['style'] . '"';
        }

        // Wrapper
        if (isset($data['url']) && 0 < strlen($url = $data['url'])) {
            $wrapStart = '<a href="' . $url . '"' . $class . '>';
            $wrapEnd = '</a>';
        } else {
            $wrapStart = '<span' . $class . '>';
            $wrapEnd = '</span>';
        }

        $view->content = $wrapStart . $content . $wrapEnd;

        return $view;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Image';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/image';
    }

    /**
     * Returns the default choices.
     *
     * @return array
     */
    static public function getDefaultStyleChoices()
    {
        return [
            'img-rounded'   => 'Rounded',
            'img-circle'    => 'Circle',
            'img-thumbnail' => 'Thumbnail',
        ];
    }
}
