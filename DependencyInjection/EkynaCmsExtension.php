<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\CmsBundle\SlideShow\Type;
use Ekyna\Bundle\ResourceBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EkynaCmsExtension
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->configure($configs, 'ekyna_cms', new Configuration(), $container);

        $container->setParameter('ekyna_cms.home_route', $config['home_route']);
        $container->setParameter('ekyna_cms.page.config', $config['page']);
        $container->setParameter('ekyna_cms.menu.config', $config['menu']);

        // Editor config
        $container
            ->getDefinition('ekyna_cms.editor.editor')
            ->replaceArgument(5, [
                'locales'                  => $container->getParameter('locales'),
                'css_path'                 => $config['editor']['css_path'],
                'layout'                   => $config['editor']['layout'],
                'viewports'                => $config['editor']['viewports'],
                'block_min_size'           => $config['editor']['plugins']['block']['min_size'],
                'default_block_plugin'     => $config['editor']['plugins']['block']['default'],
                'default_container_plugin' => $config['editor']['plugins']['container']['default'],
            ]);

        // Editor twig extension config
        $container
            ->getDefinition('ekyna_cms.twig.editor_extension')
            ->replaceArgument(4, [
                'template' => $config['editor']['template'],
            ]);

        // Editor plugins config
        foreach ($config['editor']['plugins'] as $type => $pluginsConfigs) {
            foreach ($pluginsConfigs as $name => $pluginConfig) {
                $container->setParameter('ekyna_cms.editor.' . $type . '_plugin.' . $name . '.config', $pluginConfig);
            }
        }

        // Cms twig extension config
        $container
            ->getDefinition('ekyna_cms.twig.cms_extension')
            ->replaceArgument(7, [
                'home_route' => $config['home_route'],
                'seo'        => $config['seo'],
                'page'       => $config['page'],
            ]);

        // SchemaOrg provider classes config
        $providerClasses = $config['schema_org']['provider'];
        $container
            ->getDefinition('ekyna_cms.schema_org.provider_registry')
            ->addMethodCall('registerClass', [$providerClasses]);

        // Social buttons bundle bridge
        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('EkynaSocialButtonsBundle', $bundles)) {
            $this->registerSocialSubjectEventSubscriber($container);
        }

        $this->configureSlideShow($container, $config['slide_show']);
    }

    private function configureSlideShow(ContainerBuilder $container, $config)
    {
        $registry = $container->getDefinition('ekyna_cms.slide_show.registry');

        $container->setParameter('ekyna_cms.slide_show.static', $config['static']);
        $container->setParameter('ekyna_cms.slide_show.themes', $config['themes']);

        $types = $config['types'];

        if (!isset($types['default'])) {
            $types['default'] = [
                'class'   => Type\DefaultType::class,
                'js_path' => 'ekyna-cms/slide-show/type/default',
                'label'   => 'ekyna_cms.slide.type.default.label',
                'config'  => [],
            ];
        }
        if (!isset($types['hero'])) {
            $types['hero'] = [
                'class'   => Type\HeroType::class,
                'js_path' => 'ekyna-cms/slide-show/type/hero',
                'label'   => 'ekyna_cms.slide.type.hero.label',
                'config'  => [],
            ];
        }

        foreach ($types as $name => $c) {
            $class = $c['class'];
            if (class_exists($class)) {
                $id = "ekyna_cms.slide_show.type.{$name}";
                $definition = new Definition($class, []);
                $container->setDefinition($id, $definition);
            } elseif ($container->hasDefinition($class)) {
                $id = $class;
                $definition = $container->getDefinition($class);
                $class = $definition->getClass();
            } else {
                throw new \InvalidArgumentException("Unexpected slide show type '$class'.");
            }

            if (is_subclass_of($class, Type\AbstractType::class)) {
                $definition->addMethodCall('setMediaRepository', [new Reference('ekyna_media.media.repository')]);
                $definition->addMethodCall('setMediaGenerator', [new Reference('ekyna_media.generator')]);
            }

            $definition->addMethodCall('configure', [
                $name,
                $c['label'],
                $c['js_path'],
                isset($c['config']) ? $c['config'] : [],
            ]);

            $registry->addMethodCall('register', [new Reference($id)]);
        }
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
