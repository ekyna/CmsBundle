<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\UiBundle\Model\Modal;
use Ekyna\Bundle\UiBundle\Service\Modal\ModalRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    protected array                 $config;
    protected UrlGeneratorInterface $urlGenerator;
    protected FormFactoryInterface  $formFactory;
    protected ModalRenderer         $modalRenderer;


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Sets the url generator.
     *
     * @param UrlGeneratorInterface $generator
     */
    public function setUrlGenerator(UrlGeneratorInterface $generator): void
    {
        $this->urlGenerator = $generator;
    }

    /**
     * Sets the form factory.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Sets the modalRenderer.
     *
     * @param ModalRenderer $modalRenderer
     */
    public function setModalRenderer(ModalRenderer $modalRenderer): void
    {
        $this->modalRenderer = $modalRenderer;
    }

    /**
     * Returns the plugin config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Creates a modal response.
     *
     * @param string $title
     * @param mixed  $content
     * @param array  $buttons
     *
     * @return Response
     */
    protected function createModalResponse(string $title, $content = null, array $buttons = []): Response
    {
        $modal = new Modal($title);

        if (empty($buttons)) {
            $buttons['submit'] = array_replace(Modal::BTN_SUBMIT, [
                'label' => 'button.save',
            ]);
        }
        if (!array_key_exists('close', $buttons)) {
            $buttons['close'] = Modal::BTN_CLOSE;
        }

        $modal->setButtons($buttons);

        if ($content) {
            $modal->setContent($content);
        }

        return $this->modalRenderer->render($modal);
    }
}
