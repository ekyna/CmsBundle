<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\AdminBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * EkynaCmsExtension
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsExtension extends AbstractExtension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        list($config, $loader) = $this->configure($configs, 'ekyna_cms', new Configuration(), $container);

        $container->setParameter('ekyna_cms.content_enabled', $config['enable_contents']);
        $container->setParameter('ekyna_cms.home_route_name', $config['defaults']['home_route']);
        $container->setParameter('ekyna_cms.default_template', $config['defaults']['template']);
        $container->setParameter('ekyna_cms.default_controller', $config['defaults']['controller']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = array(
            'enable_contents' => false,
            'defaults' => array(
        	    'home_route' => 'home',
        	    'template'   => 'EkynaCmsBundle:Cms:default.html.twig',
        	    'controller' => 'EkynaCmsBundle:Cms:default',
            ),
        );
        $container->prependExtensionConfig('ekyna_cms', $config);
    }
}
