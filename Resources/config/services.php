<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Action\SlideCreateFlowAction;
use Ekyna\Bundle\CmsBundle\Install\CmsInstaller;
use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\SlideShowGenerator;
use Ekyna\Bundle\CmsBundle\Service\Helper\CacheHelper;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Service\LocaleSwitcher;
use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\Builder;
use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\Registry;
use Ekyna\Bundle\CmsBundle\Service\Setting\SeoSchema;
use Ekyna\Bundle\CmsBundle\Service\Sitemap\PageProvider;
use Ekyna\Bundle\CmsBundle\Service\Updater\MenuUpdater;
use Ekyna\Bundle\CmsBundle\Service\Updater\PageRedirectionUpdater;
use Ekyna\Bundle\CmsBundle\Service\Updater\PageUpdater;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistry;
use Ekyna\Bundle\CmsBundle\SlideShow\TypeRegistryInterface;
use Ekyna\Bundle\SettingBundle\DependencyInjection\Compiler\RegisterSchemasPass;
use Ekyna\Bundle\SitemapBundle\DependencyInjection\Compiler\SitemapProviderPass;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Actions
        ->set('ekyna_cms.action.slide_create_flow', SlideCreateFlowAction::class)
            ->args([
                service('ekyna_cms.form_flow.create_slide')
            ])
            ->tag('ekyna_resource.action')

        // Menu updater
        ->set('ekyna_cms.updater.menu', MenuUpdater::class)
            ->args([
                service('ekyna_cms.repository.page'),
                service('doctrine.orm.entity_manager'),
                service('ekyna_resource.http.tag_manager'),
                param('ekyna_cms.class.menu'),
            ])

        // Page updater
        ->set('ekyna_cms.updater.page', PageUpdater::class)
            ->args([
                service('ekyna_cms.repository.page'),
                service('ekyna_cms.helper.routing'),
                service('ekyna_cms.updater.menu'),
                service('ekyna_cms.repository.menu'),
                service('doctrine.orm.entity_manager'),
                service('ekyna_resource.http.tag_manager'),
                service('ekyna_ui.helper.twig'),
                abstract_arg('Page configuration'),
            ])

        // Page redirection updater
        ->set('ekyna_cms.updater.page_redirection', PageRedirectionUpdater::class)
            ->args([
                service('event_dispatcher'),
            ])

        // Setting schema
        ->set('ekyna_cms.setting.seo_schema', SeoSchema::class)
            ->tag(RegisterSchemasPass::TAG, ['namespace' => 'seo', 'position' => 10])

        // Sitemap provider
        ->set('ekyna_cms.sitemap.page_provider', PageProvider::class)
            ->args([
                service('ekyna_cms.repository.page'),
                service('router'),
            ])
            ->tag(SitemapProviderPass::TAG)

        // Locale switcher
        ->set('ekyna_cms.locale_switcher', LocaleSwitcher::class)
            ->lazy()
            ->args([
                service('ekyna_resource.helper'),
                service('ekyna_resource.manager.factory'),
                service('router'),
                service('request_stack'),
                abstract_arg('Public locales'),
            ])
            ->alias(LocaleSwitcher::class, 'ekyna_cms.locale_switcher')

        // Cache
        ->set('ekyna_cms.cache')
            ->parent('cache.app')
            ->private()
            ->tag('cache.pool', ['clearer' => 'cache.default_clearer'])

        // Cache helper
        ->set('ekyna_cms.helper.cache', CacheHelper::class)
            ->args([
                service('ekyna_cms.cache'),
                service('doctrine.orm.default_result_cache')->nullOnInvalid(),
                param('ekyna_resource.locales'),
                param('ekyna_cms.class.page'),
            ])

        // Page helper
        ->set('ekyna_cms.helper.page', PageHelper::class)
            ->lazy()
            ->args([
                service('ekyna_cms.repository.page'),
                service('ekyna_cms.cache'),
                param('ekyna_cms.home_route'),
            ])

        // Schema.org provider registry
        ->set('ekyna_cms.schema_org.provider_registry', Registry::class)

        // Schema.org builder
        ->set('ekyna_cms.schema_org.builder', Builder::class)
            ->args([
                service('ekyna_cms.schema_org.provider_registry')
            ])
            ->tag('twig.runtime')

        // Slide show type registry
        ->set('ekyna_cms.slide_show.registry', TypeRegistry::class)
            ->alias(TypeRegistryInterface::class, 'ekyna_cms.slide_show.registry')

        // Menu generator
        ->set('ekyna_cms.generator.menu', MenuGenerator::class)
            ->args([
                service('ekyna_cms.manager.menu'),
                service('ekyna_cms.repository.menu'),
                service('ekyna_cms.factory.menu'),
                service('doctrine.orm.default_entity_manager'),
                abstract_arg('Menu configuration'),
                param('ekyna_resource.locales'),
            ])

        // Page generator
        ->set('ekyna_cms.generator.page', PageGenerator::class)
            ->args([
                service('ekyna_cms.manager.page'),
                service('ekyna_cms.repository.page'),
                service('ekyna_cms.factory.page'),
                service('ekyna_cms.manager.menu'),
                service('ekyna_cms.repository.menu'),
                service('ekyna_cms.factory.menu'),
                service('ekyna_cms.factory.seo'),
                service('validator'),
                service('ekyna_cms.helper.routing'),
                service('doctrine.orm.default_entity_manager'),
                param('ekyna_resource.locales'),
                param('ekyna_cms.home_route'),
            ])

        // Slide show generator
        ->set('ekyna_cms.generator.slide_show', SlideShowGenerator::class)
            ->args([
                service('ekyna_cms.manager.slide_show'),
                service('ekyna_cms.repository.slide_show'),
                service('ekyna_cms.factory.slide_show'),
                abstract_arg('Static slideshow configurations'),
            ])

        // Installer
        ->set('ekyna_cms.installer', CmsInstaller::class)
            ->args([
                service('ekyna_cms.generator.page'),
                service('ekyna_cms.generator.menu'),
                service('ekyna_cms.generator.slide_show'),
            ])
            ->tag('ekyna_install.installer', ['priority' => 98])
    ;
};
