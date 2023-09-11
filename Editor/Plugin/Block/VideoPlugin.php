<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\VideoBlockType;
use Ekyna\Bundle\MediaBundle\Model\AspectRatio;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\MediaRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VideoPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class VideoPlugin extends AbstractPlugin
{
    public const NAME = 'ekyna_block_video';

    private const DEFAULT_DATA = [
        'video' => [
            'responsive' => true,
            'autoplay'   => false,
            'loop'       => false,
            'muted'      => false,
            'player'     => false,
            'ratio'      => AspectRatio::RATIO_16_9,
            'height'     => null,
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

    public function __construct(
        array   $config,
        private readonly MediaRepository $mediaRepository,
        private readonly MediaRenderer $mediaRenderer
    ) {
        parent::__construct(array_replace([
            'default_poster' => '/bundles/ekynacms/img/default-image.gif',
        ], $config));
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
        $options = array_replace([
            'action' => $this->urlGenerator->generate('admin_ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method' => 'post',
            'attr'   => [
                'class' => 'form-horizontal',
            ],
        ], $options);

        $form = $this->formFactory->create(VideoBlockType::class, $block, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModalResponse('Modifier le bloc video.', $form->createView());
    }

    /**
     * @inheritDoc
     */
    public function createWidget(
        BlockInterface   $block,
        AdapterInterface $adapter,
        array            $options,
        int              $position = 0
    ): WidgetView {
        $data = array_replace_recursive(
            self::DEFAULT_DATA,
            self::DEFAULT_TRANSLATION_DATA,
            $block->getData(),
            $block->translate($this->localeProvider->getCurrentLocale(), true)->getData()
        );

        $fallbackData = $block->translate($this->localeProvider->getFallbackLocale())->getData();
        if (!isset($data['poster']['media']) && isset($fallbackData['poster']['media'])) {
            $data['poster']['media'] = $fallbackData['poster']['media'];
        }
        if (!isset($data['video']['media']) && isset($fallbackData['video']['media'])) {
            $data['video']['media'] = $fallbackData['video']['media'];
        }

        $view = parent::createWidget($block, $adapter, $options, $position);
        $view->getAttributes()->addClass('cms-video');


        /** @var MediaInterface $poster */
        $posterPath = null;
        if (isset($data['poster'])) {
            $posterData = $data['poster'];
            if (array_key_exists('media', $posterData) && 0 < $mediaId = intval($posterData['media'])) {
                if ($poster = $this->mediaRepository->find($mediaId)) {
                    $posterPath = $this->mediaRenderer->getGenerator()->generateFrontUrl($poster);
                }
            }
        }
        if (empty($posterPath)) {
            $posterPath = $this->config['default_poster'];
        }

        /** @var MediaInterface $video */
        $video = null;
        if (isset($data['video'])) {
            $videoData = $data['video'];
            if (array_key_exists('media', $videoData) && 0 < $videoId = intval($videoData['media'])) {
                $video = $this->mediaRepository->find($videoId);
            }
        }

        if (null !== $video) {
            $params = $videoData;
            $params['responsive'] = true;
            $params['aspect_ratio'] = $params['ratio'];
            $params['min_height'] = $params['height'];
            $params['poster'] = $posterPath;

            $view->content = $this->mediaRenderer->renderVideo($video, $params);
        } else {
            $view->content = '<img class="img-responsive" src="' . $posterPath . '"  alt="">'; // TODO alt
        }

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Video';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
