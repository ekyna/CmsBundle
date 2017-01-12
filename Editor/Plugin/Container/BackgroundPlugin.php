<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\BackgroundContainerType;
use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
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
            'filter'        => 'cms_container_background',
            'default_color' => '',
        ], $config));

        $this->mediaRepository = $mediaRepository;
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritdoc}
     */
    /*public function create(ContainerInterface $container, array $data = [])
    {
        parent::create($container, $data);
    }*/

    /**
     * {@inheritdoc}
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

        if ($request->getMethod() == 'POST' && $form->handleRequest($request) && $form->isValid()) {
            $data = $form->getData();

            $container->setData($data);

            return null;
        }

        return $this->createModal('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false)
    {
        $data = $container->getData();
        $attributes = $view->getAttributes();

        // Background color
        $bgColor = array_key_exists('color', $data) ? $data['color'] : $this->config['default_color'];
        if (0 < strlen($bgColor)) {
            $attributes->addStyle('background-color', $bgColor);
        }

        // PaddingTop
        $paddingTop = array_key_exists('padding_top', $data) ? intval($data['padding_top']) : 0;
        if (0 < $paddingTop) {
            $attributes->addStyle('padding-top', $paddingTop . 'px');
        }

        // PaddingBottom
        $paddingBottom = array_key_exists('padding_bottom', $data) ? intval($data['padding_bottom']) : 0;
        if (0 < $paddingBottom) {
            $attributes->addStyle('padding-bottom', $paddingBottom . 'px');
        }

        // Background image
        if (array_key_exists('media_id', $data) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                $path = $this->cacheManager->getBrowserPath($media->getPath(), $this->config['filter']);

                $attributes->addStyle('background-image', 'url(' . $path . ')');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return 'Background'; // TODO trans
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_container_background';
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/container/background';
    }
}
