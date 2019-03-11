<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\VideoBlockType;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class VideoPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class VideoPlugin extends AbstractPlugin
{
    const NAME = 'ekyna_block_video';

    private const DEFAULT_DATA = [
        'video' => [
            'responsive' => true,
            'autoplay'   => false,
            'loop'       => false,
            'muted'      => false,
            'player'     => false,
        ],
    ];

    private const DEFAULT_TRANSLATION_DATA = [
        'poster' => [
            'media' => null,
        ],
        'video'  => [
            'media' => null,
        ],
    ];

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
     * @param Renderer        $renderer
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        Renderer $renderer
    ) {
        parent::__construct(array_replace([
            'default_poster' => '/bundles/ekynacms/img/default-image.gif',
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->mediaRenderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        $block
            ->setData(array_merge(self::DEFAULT_DATA, $data))
            ->translate($this->localeProvider->getCurrentLocale(), true)
            ->setData(self::DEFAULT_TRANSLATION_DATA);
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        $this->upgrade($block);

        $options = array_replace([
            'action'     => $this->urlGenerator->generate('ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'     => 'post',
            'attr'       => [
                'class' => 'form-horizontal',
            ],
        ], $options);

        $form = $this->formFactory->create(VideoBlockType::class, $block, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModal('Modifier le bloc video.', $form->createView());
    }

    /**
     * @inheritDoc
     */
    public function createWidget(BlockInterface $block, AdapterInterface $adapter, array $options, $position = 0)
    {
        $this->upgrade($block);

        $data = array_replace_recursive(
            $block->getData(),
            $block->translate($this->localeProvider->getCurrentLocale())->getData()
        );

        $view = parent::createWidget($block, $adapter, $options, $position);
        $view->getAttributes()->addClass('cms-video');


        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $poster */
        $posterPath = null;
        $posterData = $data['poster'];
        if (array_key_exists('media', $posterData) && 0 < $mediaId = intval($posterData['media'])) {
            if ($poster = $this->mediaRepository->find($mediaId)) {
                $posterPath = $this->mediaRenderer->getGenerator()->generateFrontUrl($poster);
            }
        }
        if (empty($posterPath)) {
            $posterPath = $this->config['default_poster'];
        }

        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $video */
        $video = null;
        $videoData = $data['video'];
        if (array_key_exists('media', $videoData) && 0 < $videoId = intval($videoData['media'])) {
            $video = $this->mediaRepository->find($videoId);
        }

        if ($video) {
            $params = $videoData;
            $params['responsive'] = true;
            $params['poster'] = $posterPath;

            $view->content = $this->mediaRenderer->renderVideo($video, $params);
        } else {
            $view->content = '<img class="img-responsive" src="' . $posterPath . '"  alt="">'; // TODO alt
        }

        return $view;
    }

    /**
     * Changes the block and translation data to follow the 2019-03-11 changes (poster and video per translation).
     *
     * @param BlockInterface $block
     */
    private function upgrade(BlockInterface $block)
    {
        $data = array_replace(self::DEFAULT_DATA, $block->getData());

        $translation = $block->translate($this->localeProvider->getFallbackLocale());

        $translationData = array_replace(self::DEFAULT_TRANSLATION_DATA, $translation->getData());

        if (isset($data['poster'])) {
            if (isset($data['poster']['media'])) {
                $translationData['poster']['media'] = $data['poster']['media'];
            }
            unset($data['poster']);
        }
        if (isset($data['video']) && isset($data['video']['media'])) {
            $translationData['video']['media'] = $data['video']['media'];
            unset($data['video']['media']);
        }

        $block->setData($data);
        $translation->setData($translationData);
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Video';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return static::NAME;
    }
}
