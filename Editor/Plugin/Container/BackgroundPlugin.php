<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BackgroundContainerType;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\MediaRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function __construct(
        array                            $config,
        private readonly MediaRepository $mediaRepository,
        private readonly MediaRenderer   $mediaRenderer
    ) {
        parent::__construct(array_replace([
            'filter'        => 'cms_container_background',
            'themes'        => PropertyDefaults::getDefaultThemeChoices(),
            'default_color' => '',
        ], $config));
    }

    /**
     * @inheritDoc
     */
    public function create(ContainerInterface $container, array $data = []): void
    {
        //parent::create($container, $data);

        $container->setData(array_merge(self::DEFAULT_DATA, [
            'color' => $this->config['default_color'],
        ], $data));
    }

    /**
     * @inheritDoc
     */
    public function update(ContainerInterface $container, Request $request): ?Response
    {
        $this->upgrade($container);

        $form = $this
            ->formFactory
            ->create(BackgroundContainerType::class, $container, [
                'action' => $this->urlGenerator->generate(
                    'admin_ekyna_cms_editor_container_edit',
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

        return $this->createModalResponse('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * Changes the container data to follow the 2019-03-11 changes.
     *
     * @param ContainerInterface $container
     */
    private function upgrade(ContainerInterface $container): void
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
     * @inheritDoc
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false): void
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
        if (!empty($bgColor)) {
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
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Background'; // TODO trans
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'ekyna_container_background';
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/container/background';
    }
}
