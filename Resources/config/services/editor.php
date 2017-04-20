<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Editor\Locale\DocumentLocaleProvider;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\AbstractPlugin as AbstractBlockPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\FeaturePlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\ImagePlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\TabsPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\TemplatePlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\TinymcePlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\VideoPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\AbstractPlugin as AbstractContainerPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\BackgroundPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\CopyPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistry;
use Ekyna\Bundle\CmsBundle\Editor\Renderer\EditorRenderer;
use Ekyna\Bundle\CmsBundle\Editor\Repository\Repository;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Content locale provider
        ->set('ekyna_cms.editor.document_locale_provider', DocumentLocaleProvider::class)
            ->args([
                param('ekyna_resource.locales'),
                param('kernel.default_locale'),
            ])
            ->tag('kernel.event_subscriber', ['priority' => 98])

        // Editor repository
        ->set('ekyna_cms.editor.repository', Repository::class)
            ->args([
                service('ekyna_resource.factory.factory'),
                service('ekyna_resource.repository.factory'),
                abstract_arg('Editor entities interface/class map'),
            ])

        // Plugin registry
        ->set('ekyna_cms.editor.plugin_registry', PluginRegistry::class)

        // Editor
        ->set('ekyna_cms.editor.editor', Editor::class)
            ->lazy(true)
            ->args([
                service('ekyna_cms.editor.repository'),
                service('ekyna_cms.editor.plugin_registry'),
                service('ekyna_cms.editor.document_locale_provider'),
                service('ekyna_cms.helper.page'),
                abstract_arg('Editor configuration'),
            ])

        // Renderer
        ->set('ekyna_cms.editor.renderer', EditorRenderer::class)
            ->args([
                service('ekyna_cms.editor.editor'),
                service('ekyna_cms.helper.page'),
                service('ekyna_resource.http.tag_manager'),
                service('twig'),
                service('doctrine.orm.entity_manager'),
                abstract_arg('Editor renderer configuration'),
            ])
            ->tag('twig.runtime')
    ;

    // ------------------------------------------------------------------------------------

    $container
        ->services()

        // Abstract block plugin
        ->set('ekyna_cms.editor.block_plugin.abstract', AbstractBlockPlugin::class)
            ->abstract()
            ->call('setUrlGenerator', [service('router')])
            ->call('setFormFactory', [service('form.factory')])
            ->call('setModalRenderer', [service('ekyna_ui.modal.renderer')])
            ->call('setLocaleProvider', [service('ekyna_cms.editor.document_locale_provider')])

        // Tinymce block plugin
        ->set('ekyna_cms.editor.block_plugin.tinymce', TinymcePlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Tinymce block plugin configuration'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')

        // Image block plugin
        ->set('ekyna_cms.editor.block_plugin.image', ImagePlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Image block plugin configuration'),
                service('ekyna_media.repository.media'),
                service('ekyna_media.generator'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')

        // Video block plugin
        ->set('ekyna_cms.editor.block_plugin.video', VideoPlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Video block plugin configuration'),
                service('ekyna_media.repository.media'),
                service('ekyna_media.renderer'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')

        // Feature block plugin
        ->set('ekyna_cms.editor.block_plugin.feature', FeaturePlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Feature block plugin configuration'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')

        // Template block plugin
        ->set('ekyna_cms.editor.block_plugin.template', TemplatePlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Template block plugin configuration'),
                service('twig'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')

        // Tabs block plugin
        ->set('ekyna_cms.editor.block_plugin.tabs', TabsPlugin::class)
            ->parent('ekyna_cms.editor.block_plugin.abstract')
            ->args([
                abstract_arg('Tabs block plugin configuration'),
                service('serializer'),
                service('twig'),
            ])
            ->tag('ekyna_cms.editor.block_plugin')
    ;

    // ------------------------------------------------------------------------------------

    $container
        ->services()

        // Abstract container plugin
        ->set('ekyna_cms.editor.container_plugin.abstract', AbstractContainerPlugin::class)
            ->abstract()
            ->call('setUrlGenerator', [service('router')])
            ->call('setFormFactory', [service('form.factory')])
            ->call('setModalRenderer', [service('ekyna_ui.modal.renderer')])

        // Copy container plugin
        ->set('ekyna_cms.editor.container_plugin.copy', CopyPlugin::class)
            ->parent('ekyna_cms.editor.container_plugin.abstract')
            ->args([
                [], //abstract_arg('Copy container plugin configuration'),
            ])
            ->tag('ekyna_cms.editor.container_plugin')

        // Background container plugin
        ->set('ekyna_cms.editor.container_plugin.background', BackgroundPlugin::class)
            ->parent('ekyna_cms.editor.container_plugin.abstract')
            ->args([
                abstract_arg('Background container plugin configuration'),
                service('ekyna_media.repository.media'),
                service('ekyna_media.renderer'),
            ])
            ->tag('ekyna_cms.editor.container_plugin')

    ;
};
