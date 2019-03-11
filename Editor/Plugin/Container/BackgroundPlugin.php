<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BackgroundContainerType;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BackgroundPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BackgroundPlugin extends AbstractPlugin
{
    private const DEFAULT_DATA = [
        'image' => [
            'media' => null,
        ],
        'video' => [
            'media' => null,
        ],
        'color' => null,
        'theme' => null,
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
            'filter'                 => 'cms_container_background',
            'themes'                 => PropertyDefaults::getDefaultThemeChoices(),
            'default_color'          => '',
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->mediaRenderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function create(ContainerInterface $container, array $data = [])
    {
        //parent::create($container, $data);

        $container->setData(array_merge(self::DEFAULT_DATA, [
            'color' => $this->config['default_color'],
        ], $data));
    }

    /**
     * @inheritdoc
     */
    public function update(ContainerInterface $container, Request $request)
    {
        $this->upgrade($container);

        $form = $this
            ->formFactory
            ->create(BackgroundContainerType::class, $container, [
                'action' => $this->urlGenerator->generate(
                    'ekyna_cms_editor_container_edit',
                    ['containerId' => $container->getId()]
                ),
                'method' => 'post',
                'themes' => $this->config['themes'],
                'attr'   => [
                    'class' => 'form-horizontal',
                ],
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModal('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * Changes the container data to follow the 2019-03-11 changes.
     *
     * @param ContainerInterface $container
     */
    private function upgrade(ContainerInterface $container)
    {
        $data = array_replace(self::DEFAULT_DATA, $container->getData());

        if (isset($data['media_id'])) {
            $data['image']['media'] = $data['media_id'];
            unset($data['media_id']);
        }
        if (isset($data['video_id'])) {
            $data['video']['media'] = $data['video_id'];
            unset($data['video_id']);
        }

        unset($data['padding_top']);
        unset($data['padding_bottom']);

        $container->setData($data);
    }

    /**
     * @inheritdoc
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false)
    {
        $this->upgrade($container);

        $data = $container->getData();

        $attributes = $view->getAttributes();
        $attributes->addClass('cms-background');

        if (isset($data['theme']) && !empty($data['theme'])) {
            $attributes->addClass($data['theme']);
        }

        // Background color
        $bgColor = array_key_exists('color', $data) ? $data['color'] : $this->config['default_color'];
        if (0 < strlen($bgColor)) {
            $attributes->addStyle('background-color', $bgColor);
        }

        // Background image
        if (0 < $id = intval($data['image']['media'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $image */
            if (null !== $image = $this->mediaRepository->find($id)) {
                $path = $this
                    ->mediaRenderer
                    ->getGenerator()
                    ->generateFrontUrl($image, $this->config['filter']);

                $attributes->addStyle('background-image', 'url(' . $path . ')');
            }
        }

        // Background video
        if (0 < $id = intval($data['video']['media'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $video */
            if (null !== $video = $this->mediaRepository->find($id)) {
                $attributes->addClass('cms-background-video');

                $view->content = $this->mediaRenderer->renderVideo($video, [
                    'responsive'  => false,
                    'autoplay'    => true,
                    'loop'        => true,
                    'muted'       => true,
                    'player'      => false,
                    'alt_message' => null,
                ]);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Background'; // TODO trans
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ekyna_container_background';
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/container/background';
    }
}
