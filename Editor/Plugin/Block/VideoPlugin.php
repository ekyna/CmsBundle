<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\VideoBlockType;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class VideoPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class VideoPlugin extends AbstractPlugin
{
    const NAME = 'ekyna_block_video';


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
            'default_poster' => '/bundles/ekynacms/img/default-image.gif',
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

        $defaultData = [
            'poster' => [
                'media' => null,
            ],
            'video'  => [
                'media'      => null,
                'responsive' => true,
                'autoplay'   => false,
                'loop'       => false,
                'muted'      => false,
                'player'     => false,
            ],
        ];

        $block->setData(array_merge($defaultData, $data));
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        $options = array_replace([
            'repository' => $this->mediaRepository,
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

        $form = $this->formFactory->create(VideoBlockType::class, $block->getData(), $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $block->setData($form->getData());

            return null;
        }

        return $this->createModal('Modifier le bloc video.', $form->createView());
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
        // TODO removed undefined data indexes
    }

    /**
     * @inheritDoc
     */
    public function createWidget(BlockInterface $block, array $options, $position = 0)
    {
        $data = $block->getData();

        $view = parent::createWidget($block, $options, $position);
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

    /**
     * @inheritdoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/video';
    }
}
