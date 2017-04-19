<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BackgroundContainerType;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class BackgroundPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class BackgroundPlugin extends AbstractPlugin
{
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
     * @param Renderer       $renderer
     */
    public function __construct(
        array $config,
        MediaRepository $mediaRepository,
        Renderer $renderer
    ) {
        parent::__construct(array_replace([
            'filter'                 => 'cms_container_background',
            'default_color'          => '',
            'default_padding_top'    => 0,
            'default_padding_bottom' => 0,
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->mediaRenderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    /*public function create(ContainerInterface $container, array $data = [])
    {
        parent::create($container, $data);
    }*/

    /**
     * @inheritdoc
     */
    public function update(ContainerInterface $container, Request $request)
    {
        $form = $this->formFactory->create(BackgroundContainerType::class, $container->getData(), [
            'repository' => $this->mediaRepository,
            'action'     => $this->urlGenerator->generate(
                'ekyna_cms_editor_container_edit',
                ['containerId' => $container->getId()]
            ),
            'method'     => 'post',
            'attr'       => [
                'class' => 'form-horizontal',
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $container->setData($data);

            return null;
        }

        return $this->createModal('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * @inheritdoc
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context)
    {

    }

    /**
     * @inheritdoc
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false)
    {
        $data = $container->getData();
        $attributes = $view->getAttributes();
        $attributes->addClass('cms-background');

        // Background color
        $bgColor = array_key_exists('color', $data) ? $data['color'] : $this->config['default_color'];
        if (0 < strlen($bgColor)) {
            $attributes->addStyle('background-color', $bgColor);
        }

        // Padding top
        $paddingTop = array_key_exists('padding_top', $data)
            ? intval($data['padding_top'])
            : $this->config['default_padding_top'];
        if (0 < $paddingTop) {
            $attributes->addStyle('padding-top', $paddingTop . 'px');
        }

        // Padding bottom
        $paddingBottom = array_key_exists('padding_bottom', $data)
            ? intval($data['padding_bottom'])
            : $this->config['default_padding_bottom'];
        if (0 < $paddingBottom) {
            $attributes->addStyle('padding-bottom', $paddingBottom . 'px');
        }

        // Background image
        if (array_key_exists('media_id', $data) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                $path = $this
                    ->mediaRenderer
                    ->getGenerator()
                    ->generateFrontUrl($media, $this->config['filter']);

                $attributes->addStyle('background-image', 'url(' . $path . ')');
            }
        }

        // Background video
        if (array_key_exists('video_id', $data) && 0 < $videoId = intval($data['video_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $video */
            if (null !== $video = $this->mediaRepository->find($videoId)) {
                $attributes->addClass('cms-background-video');

                $view->content = $this->mediaRenderer->renderVideo($video, [
                    'responsive'   => false,
                    'autoplay'     => true,
                    'loop'         => true,
                    'muted'        => true,
                    'player'       => false,
                    'alt_message'  => null,
                ]);

                /*$view->content = '<video playsinline autoplay muted loop>'; // poster=""
                $view->content .= '<source src="' . $path . '" type="' . $mimeType . '">';
                $view->content .= '</video>';*/
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
