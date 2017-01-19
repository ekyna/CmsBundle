<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\ImageBlockType;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
     * @var CacheManager
     */
    private $cacheManager;


    /**
     * Constructor.
     *
     * @param array           $config
     * @param MediaRepository $mediaRepository
     * @param CacheManager    $cacheManager
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        CacheManager $cacheManager
    ) {
        parent::__construct(array_replace([
            'default_path' => '/bundles/ekynacms/img/default-image.gif',
            'default_alt'  => 'Default image',
            'filter'       => 'cms_block_image', // TODO config
            'styles'       => static::getDefaultStyleChoices(),
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->cacheManager = $cacheManager;
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

        if ($request->getMethod() == 'POST' && $form->handleRequest($request) && $form->isValid()) {
            $data = $form->getData();

            $block->setData('media_id', $data['media_id']);
            $block->setData('style', $data['style']);
            $block->setData('url', $data['url']);

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
        $data = $block->getData();

        if (!array_key_exists('media_id', $data)) {
            $context->addViolation(self::INVALID_DATA);
        }

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

        $path = $options['default_path'];
        $alt = $options['default_alt'];
        $class = '';

        $data = $block->getData();
        if (isset($data['media_id']) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                // TODO use MediaPlayer / MediaGenerator
                $path = $this->cacheManager->getBrowserPath($media->getPath(), $options['filter']);
                $alt = $media->getTitle();
            }
        }

        // Style
        if (isset($data['style']) && isset($this->config['styles'][$data['style']])) {
            $class = $data['style'];
        }

        // Link
        if (isset($data['url']) && 0 < strlen($data['url'])) {
            /** @noinspection HtmlUnknownTarget */
            /** @noinspection HtmlUnknownAttribute */
            $view->content = sprintf(
                '<a href="%s"%s style="display: inline-block"><img src="%s" alt="%s" class="img-responsive"></a>',
                $data['url'], 0 < strlen($class) ? ' class="' . $class . '"' : '', $path, $alt
            );
        } else {
            /** @noinspection HtmlUnknownTarget */
            $view->content = sprintf(
                '<img src="%s" alt="%s" class="img-responsive %s">',
                $path, $alt, $class
            );
        }

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
