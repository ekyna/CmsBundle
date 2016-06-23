<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\AbstractPlugin as BasePlugin;
use Ekyna\Bundle\CmsBundle\Model\ContainerInterface;
use Ekyna\Bundle\CoreBundle\Modal\Modal;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin extends BasePlugin implements PluginInterface
{
    const INVALID_DATA = 'ekyna_cms.container.invalid_data';


    /**
     * {@inheritdoc}
     */
    public function create(ContainerInterface $container, array $data = [])
    {
        $container->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ContainerInterface $container)
    {
        $container->setData([]);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ContainerInterface $container)
    {
        return $container->getType() === $this->getName();
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

        $buttons = [];

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
