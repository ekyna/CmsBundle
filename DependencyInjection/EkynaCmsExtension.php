<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\CmsBundle\Editor\Model as Editor;
use Ekyna\Bundle\CmsBundle\EventListener\SocialSubjectEventListener;
use Ekyna\Bundle\CmsBundle\SlideShow\Type;
use Ekyna\Bundle\ResourceBundle\DependencyInjection\PrependBundleConfigTrait;
use InvalidArgumentException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

use function array_key_exists;
use function in_array;

/**
 * Class EkynaCmsExtension
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaCmsExtension extends Extension implements PrependExtensionInterface
{
    use PrependBundleConfigTrait;

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('ekyna_cms.home_route', $config['home_route']);

        $this->prependBundleConfigFiles($container);

        $this->registerImageFilters($container);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services/console.php');
        $loader->load('services/controller.php');
        $loader->load('services/editor.php');
        $loader->load('services/form.php');
        $loader->load('services/listener.php');
        $loader->load('services/menu.php');
        $loader->load('services/resource.php');
        $loader->load('services/router.php');
        $loader->load('services/serializer.php');
        $loader->load('services/show.php');
        $loader->load('services/table.php');
        $loader->load('services/twig.php');
        $loader->load('services/validator.php');
        $loader->load('services.php');

        if (in_array($container->getParameter('kernel.environment'), ['dev', 'test'], true)) {
            $loader->load('services/dev.php');
        }

        // Menu generator
        $container
            ->getDefinition('ekyna_cms.generator.menu')
            ->replaceArgument(4, $config['menu']);

        // Page updater
        $container
            ->getDefinition('ekyna_cms.updater.page')
            ->replaceArgument(7, $config['page']);

        // Page form type
        $container
            ->getDefinition('ekyna_cms.form_type.page')
            ->replaceArgument(0, $config['page']);

        // Route provider
        $container
            ->getDefinition('ekyna_cms.routing.route_provider')
            ->replaceArgument(2, $config['page']);

        // Routing loader
        $container
            ->getDefinition('ekyna_cms.routing.loader')
            ->replaceArgument(0, $config['page']);

        // Page validator
        $container
            ->getDefinition('ekyna_cms.validator.page')
            ->replaceArgument(2, $config['page']['controllers']);

        // Locale switcher
        $container
            ->getDefinition('ekyna_cms.locale_switcher')
            ->replaceArgument(4, $config['public_locales']);

        // Notice renderer config
        $container
            ->getDefinition('ekyna_cms.renderer.notice')
            ->replaceArgument(2, $config['notice']);

        // Cms renderer config
        $container
            ->getDefinition('ekyna_cms.renderer.cms')
            ->replaceArgument(4, [
                'home_route' => $config['home_route'],
                'seo'        => $config['seo'],
                'page'       => $config['page'],
            ]);

        $this->configureEditor($container, $config['editor']);
        $this->configureSchemaOrg($container, $config['schema_org']);
        $this->configureSocialButtons($container);
        $this->configureSlideShow($container, $config['slide_show']);
    }

    private function configureEditor(ContainerBuilder $container, array $config): void
    {
        // Editor config
        $container
            ->getDefinition('ekyna_cms.editor.editor')
            ->replaceArgument(4, [
                'locales'                  => $container->getParameter('ekyna_resource.locales'),
                'css_path'                 => $config['css_path'],
                'layout'                   => $config['layout'],
                'viewports'                => $config['viewports'],
                'block_min_size'           => $config['plugins']['default']['min_size'],
                'default_block_plugin'     => $config['plugins']['default']['block'],
                'default_container_plugin' => $config['plugins']['default']['container'],
            ]);

        // Editor repository
        $container
            ->getDefinition('ekyna_cms.editor.repository')
            ->replaceArgument(2, [
                Editor\ContentInterface::class   => $container->getParameter('ekyna_cms.class.content'),
                Editor\ContainerInterface::class => $container->getParameter('ekyna_cms.class.container'),
                Editor\RowInterface::class       => $container->getParameter('ekyna_cms.class.row'),
                Editor\BlockInterface::class     => $container->getParameter('ekyna_cms.class.block'),
            ]);

        // Editor renderer config
        $container
            ->getDefinition('ekyna_cms.editor.renderer')
            ->replaceArgument(5, [
                'template' => $config['template'],
            ]);

        // Editor plugins config
        foreach (['block', 'container'] as $type) {
            foreach ($config['plugins'][$type] as $name => $pluginConfig) {
                $container
                    ->getDefinition('ekyna_cms.editor.' . $type . '_plugin.' . $name)
                    ->replaceArgument(0, $pluginConfig);
            }
        }
    }

    private function configureSchemaOrg(ContainerBuilder $container, array $config): void
    {
        // SchemaOrg provider classes config
        $providerClasses = $config['provider'];
        $container
            ->getDefinition('ekyna_cms.schema_org.provider_registry')
            ->addMethodCall('registerClass', [$providerClasses]);
    }

    private function configureSocialButtons(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!array_key_exists('EkynaSocialButtonsBundle', $bundles)) {
            return;
        }

        $definition = new Definition(SocialSubjectEventListener::class);
        $definition->addArgument(new Reference('ekyna_cms.helper.page'));
        $definition->addArgument(new Reference('router'));
        $definition->addTag('kernel.event_subscriber');
        $container->setDefinition('ekyna_cms.listener.social_subject', $definition);
    }

    private function configureSlideShow(ContainerBuilder $container, array $config): void
    {
        $registry = $container->getDefinition('ekyna_cms.slide_show.registry');

        $container
            ->getDefinition('ekyna_cms.generator.slide_show')
            ->replaceArgument(3, $config['static']);

        $container
            ->getDefinition('ekyna_cms.form_type.slide_theme')
            ->replaceArgument(0, $config['themes']);

        $types = $config['types'];

        if (!isset($types['default'])) {
            $types['default'] = [
                'class'   => Type\DefaultType::class,
                'js_path' => 'ekyna-cms/slide-show/type/default',
                'label'   => 'slide.type.default.label',
                'domain'  => 'EkynaCms',
                'config'  => [],
            ];
        }
        if (!isset($types['hero'])) {
            $types['hero'] = [
                'class'   => Type\HeroType::class,
                'js_path' => 'ekyna-cms/slide-show/type/hero',
                'label'   => 'slide.type.hero.label',
                'domain'  => 'EkynaCms',
                'config'  => [],
            ];
        }

        foreach ($types as $name => $c) {
            $class = $c['class'];
            if (class_exists($class)) {
                $id = "ekyna_cms.slide_show.type.$name";
                $definition = new Definition($class, []);
                $container->setDefinition($id, $definition);
            } elseif ($container->hasDefinition($class)) {
                $id = $class;
                $definition = $container->getDefinition($class);
                $class = $definition->getClass();
            } else {
                throw new InvalidArgumentException("Unexpected slide show type '$class'.");
            }

            if (is_subclass_of($class, Type\AbstractType::class)) {
                $definition->addMethodCall('setMediaRepository', [new Reference('ekyna_media.repository.media')]);
                $definition->addMethodCall('setMediaGenerator', [new Reference('ekyna_media.generator')]);
            }

            $definition->addMethodCall('configure', [
                $name,
                $c['label'],
                $c['js_path'],
                $c['config'] ?? [],
                $c['domain'],
            ]);

            $registry->addMethodCall('register', [new Reference($id)]);
        }
    }

    private function registerImageFilters(ContainerBuilder $container): void
    {
        $medias = [
            'lg' => 1140,
            'md' => 940,
            'sm' => 720,
            'xs' => 480,
        ];

        $filterSets = [];

        foreach ($medias as $size => $width) {
            for ($column = 1; $column <= 12; $column++) {
                $number = 12 / $column;

                $filterSets[sprintf('col_%s_%d', $size, $column)] = [
                    'cache'           => 'local_media',
                    'data_loader'     => 'local_media',
                    'filters'         => [
                        'relative_resize' => ['widen' => round(($width - ($number - 1) * 30) / $number)],
                    ],
                    'post_processors' => [
                        'jpegoptim' => [
                            'strip_all'   => true,
                            'max'         => '%ekyna_media.image.quality.jpeg%',
                            'progressive' => true,
                        ],
                        'pngquant'  => [
                            'quality' => '%ekyna_media.image.quality.png%',
                        ],
                    ],
                ];
            }
        }

        $container->prependExtensionConfig('liip_imagine', [
            'filter_sets' => $filterSets,
        ]);
    }
}
