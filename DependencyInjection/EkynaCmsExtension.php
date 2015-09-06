<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\AdminBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EkynaCmsExtension
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->configure($configs, 'ekyna_cms', new Configuration(), $container);

        $container->setParameter('ekyna_cms.home_route', $config['home_route']);
        $container->setParameter('ekyna_cms.esi_flashes', $config['esi_flashes']);

        $container->setParameter('ekyna_cms.seo.config', $config['seo']);
        $container->setParameter('ekyna_cms.page.config', $config['page']);
        $container->setParameter('ekyna_cms.menu.config', $config['menu']);
        $container->setParameter('ekyna_cms.cookie_consent.config', $config['cookie_consent']);

        foreach ($config['editor']['plugin'] as $name => $editorPluginConfig) {
            $container->setParameter('ekyna_cms.editor.plugin.'.$name.'.config', $editorPluginConfig);
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('EkynaSocialButtonsBundle', $bundles)) {
            $this->registerSocialSubjectEventSubscriber($container);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        parent::prepend($container);

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
    private function configureAsseticBundle(ContainerBuilder $container, array $config)
    {
        $asseticConfig = new AsseticConfiguration;
        $container->prependExtensionConfig('assetic', array(
            'assets' => $asseticConfig->build($config),
            'bundles' => array('EkynaCmsBundle')
        ));
    }

    /**
     * Registers the social subject event subscriber.
     *
     * @param ContainerBuilder $container
     */
    private function registerSocialSubjectEventSubscriber(ContainerBuilder $container)
    {
        $definition = new Definition('Ekyna\Bundle\CmsBundle\EventListener\SocialSubjectEventListener');
        $definition->addArgument(new Reference('ekyna_cms.helper.page'));
        $definition->addArgument(new Reference('router'));
        $definition->addTag('kernel.event_subscriber');
        $container->setDefinition('ekyna_cms.social_subject_event_listener', $definition);
    }
}
