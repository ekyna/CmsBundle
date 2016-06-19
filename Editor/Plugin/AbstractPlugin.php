<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CoreBundle\Modal\Modal;
use Ekyna\Bundle\CoreBundle\Modal\Renderer as ModalRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ModalRenderer
     */
    protected $modalRenderer;


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
    public function setUrlGenerator(UrlGeneratorInterface $generator)
    {
        $this->urlGenerator = $generator;
    }

    /**
     * Sets the form factory.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Sets the modal renderer.
     *
     * @param ModalRenderer $renderer
     */
    public function setModalRenderer(ModalRenderer $renderer)
    {
        $this->modalRenderer = $renderer;
    }

    /**
     * Returns the plugin config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Creates a modal.
     *
     * @param string $title
     * @param mixed  $content
     * @param array  $buttons
     *
     * @return Modal
     */
    protected function createModal($title, $content = null, array $buttons = [])
    {
        $modal = new Modal($title);

        if (empty($buttons)) {
            $buttons['submit'] = [
                'id'       => 'submit',
                'label'    => 'ekyna_core.button.save',
                'icon'     => 'glyphicon glyphicon-ok',
                'cssClass' => 'btn-success',
                'autospin' => true,
            ];
        }
        if (!array_key_exists('close', $buttons)) {
            $buttons['close'] = [
                'id'       => 'close',
                'label'    => 'ekyna_core.button.cancel',
                'icon'     => 'glyphicon glyphicon-remove',
                'cssClass' => 'btn-default',
            ];
        }

        $modal->setButtons($buttons);

        if ($content) {
            $modal->setContent($content);
        }

        return $modal;
    }
}
