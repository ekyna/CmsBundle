<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\ImageBlockType;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
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
     * @param array                $config
     * @param MediaRepository      $mediaRepository
     * @param CacheManager         $cacheManager
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        CacheManager $cacheManager
    ) {
        parent::__construct(array_replace([
            'default_path' => '/bundles/ekynacms/img/default-image.gif',
            'default_alt' => 'Default image',
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritDoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        // TODO $defaultData
        //$defaultData = array_key_exists('default_data', $this->config) ? $this->config['default_data'] : array();

        $block->setData([
            'media_id' => null,
        ]);

        $block->translate($this->localeProvider->getCurrentLocale(), true)->setData([]);
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, Request $request)
    {
        $form = $this->formFactory->create(ImageBlockType::class, $block->getData(), [
            'repository' => $this->mediaRepository,
            'action' => $this->urlGenerator->generate(
                'ekyna_cms_editor_block_edit',
                [
                    'blockId' => $block->getId(),
                    '_content_locale' => '', // TODO
                ]
            ),
            'method' => 'post',
            'attr' => [
                'class' => 'form-horizontal'
            ]
        ]);

        if ($request->getMethod() == 'POST' && $form->handleRequest($request)) {
            $data = $form->getData();

            $block->setData($data);

            return null;
        }

        $modal = $this->createModal('Modifier le bloc image.');
        $modal->setContent($form->createView());

        return $this->modalRenderer->render($modal);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(BlockInterface $block)
    {
        parent::remove($block);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {
        $data = $block->getData();

        if (!array_key_exists('media_id', $data)) {
            $context->addViolation(self::INVALID_DATA);
        }

        foreach ($block->getTranslations() as $blockTranslation) {
            $translationData = $blockTranslation->getData();

            if (0 < count($translationData)) {
                $context->addViolation(self::INVALID_DATA);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(BlockInterface $block, BlockView $view)
    {
        $path = $this->config['default_path'];
        $alt = $this->config['default_alt'];

        $data = $block->getData();
        if (array_key_exists('media_id', $data) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                // TODO use MediaPlayer / MediaGenerator
                $path = $this->cacheManager->getBrowserPath($media->getPath(), 'media_front');
                $alt = $media->getTitle();
            }
        }

        /** @noinspection HtmlUnknownTarget */
        $view->content = sprintf('<img src="%s" alt="%s" class="img-responsive" />', $path, $alt);
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'ekyna_block_image';
    }

    /**
     * {@inheritDoc}
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/image';
    }
}
