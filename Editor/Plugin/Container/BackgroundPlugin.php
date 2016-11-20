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
            'action' => $this->urlGenerator->generate(
                'ekyna_cms_editor_container_edit',
                ['containerId' => $container->getId()]
            ),
            'method' => 'post',
            'attr' => [
                'class' => 'form-horizontal'
            ]
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
    /*public function remove(ContainerInterface $container)
    {
        parent::remove($container);
    }*/

    /**
     * {@inheritdoc}
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context)
    {
        //$data = $container->getData();

        /* TODO if (array_key_exists('media_id', $data) && !(is_int($data['media_id']) && 0 < $data['media_id'])) {
            $context->addViolation(self::INVALID_DATA);
        }*/
    }

    /**
     * {@inheritdoc}
     */
    public function render(ContainerInterface $container, ContainerView $view)
    {
        $style = '';

        $data = $container->getData();

        // Background color
        $bgColor = array_key_exists('color', $data) ? $data['color'] : $this->config['default_color'];
        if (0 < strlen($bgColor)) {
            $style .= 'background-color:' . $bgColor . ';';
        }

        // Background image
        if (array_key_exists('media_id', $data) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                // TODO use MediaPlayer / MediaGenerator
                $path = $this->cacheManager->getBrowserPath($media->getPath(), 'media_front');

                $style .= 'background-image: url('.$path.');';
            }
        }

        if (0 < strlen($style)) {
            $view->attributes['style'] = $style;
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
