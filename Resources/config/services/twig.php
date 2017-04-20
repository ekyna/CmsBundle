<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Service\Renderer\CmsRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\LocaleSwitcherRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\MediaRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\MenuRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\NoticeRenderer;
use Ekyna\Bundle\CmsBundle\SlideShow\SlideShowRenderer;
use Ekyna\Bundle\CmsBundle\Twig\CmsExtension;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Cms renderer
        ->set('ekyna_cms.renderer.cms', CmsRenderer::class)
            ->args([
                service('ekyna_setting.manager'),
                service('ekyna_cms.helper.page'),
                service('ekyna_cms.factory.seo'),
                service('ekyna_resource.http.tag_manager'),
                abstract_arg('Cms renderer configuration'),
            ])
            ->tag('twig.runtime')

        // Locale switcher renderer
        ->set('ekyna_cms.renderer.locale_switcher', LocaleSwitcherRenderer::class)
            ->args([
                service('ekyna_cms.locale_switcher'),
                service('ekyna_resource.provider.locale'),
                service('ekyna_cms.helper.page'),
                service('twig'),
            ])
            ->tag('twig.runtime')

        // Menu renderer
        ->set('ekyna_cms.renderer.menu', MenuRenderer::class)
            ->args([
                service('ekyna_cms.menu.menu_provider'),
                service('knp_menu.helper'),
                service('ekyna_resource.http.tag_manager'),
            ])
            ->tag('twig.runtime')

        // Media renderer
        ->set('ekyna_cms.renderer.media', MediaRenderer::class)
            ->args([
                service('liip_imagine.cache.manager'),
            ])
            ->tag('twig.runtime')

        // Notice renderer
        ->set('ekyna_cms.renderer.notice', NoticeRenderer::class)
            ->args([
                service('ekyna_cms.repository.notice'),
                service('twig'),
                abstract_arg('Notice default template'),
            ])
            ->tag('twig.runtime')

        // Slide show renderer
        ->set('ekyna_cms.slide_show.renderer', SlideShowRenderer::class)
            ->args([
                service('ekyna_cms.slide_show.registry'),
                service('ekyna_cms.repository.slide_show'),
            ])
            ->tag('twig.runtime')

        // Cms extension
        ->set('ekyna_cms.twig.extension.cms', CmsExtension::class)
            ->tag('twig.extension')
    ;
};
