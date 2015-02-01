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
        $config = $this->configure($configs, 'ekyna_cms', new Configuration(), $container);

        $container->setParameter('ekyna_cms.home_route_name', $config['defaults']['home_route']);
        $container->setParameter('ekyna_cms.default_template', $config['defaults']['template']);
        $container->setParameter('ekyna_cms.default_controller', $config['defaults']['controller']);

        $container->setParameter('ekyna_cms.esi_flashes', $config['esi_flashes']);

        $container->setParameter('ekyna_cms.seo.no_follow', $config['seo']['no_follow']);
        $container->setParameter('ekyna_cms.seo.no_index', $config['seo']['no_index']);

        $container->setParameter('ekyna_cms.menus', $config['menus']);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        if (array_key_exists('TwigBundle', $bundles)) {
            $this->configureTwigBundle($container);
        }
        if (array_key_exists('AsseticBundle', $bundles)) {
            $this->configureAsseticBundle($container, $config);
        }
    }

    /**
     * Configures the TwigBundle.
     *
     * @param ContainerBuilder $container
     */
    private function configureTwigBundle(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', array(
            'form' => array('resources' => array('EkynaCmsBundle:Form:form_div_layout.html.twig')),
        ));
    }

    /**
     * Configures the AsseticBundle.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function configureAsseticBundle(ContainerBuilder $container, array $config)
    {
        $asseticConfig = new AsseticConfiguration;
        $container->prependExtensionConfig('assetic', array(
            'assets' => $asseticConfig->build($config),
        ));
    }
}
