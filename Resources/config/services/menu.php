<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Service\Menu\BreadcrumbBuilder;
use Ekyna\Bundle\CmsBundle\Service\Menu\MenuProvider;
use Knp\Menu\MenuItem;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Menu provider
        ->set('ekyna_cms.menu.menu_provider', MenuProvider::class)
            ->args([
                service('event_dispatcher'),
                service('knp_menu.factory'),
                service('ekyna_cms.repository.menu'),
                service('ekyna_resource.provider.locale'),
            ])
            ->tag('knp_menu.provider')

        // Menu builder
        ->set('ekyna_cms.menu.breadcrumb_builder', BreadcrumbBuilder::class)
            ->args([
                service('knp_menu.factory'),
                service('router'),
                service('ekyna_cms.helper.page'),
                service('ekyna_resource.provider.locale'),
                service('ekyna_resource.http.tag_manager'),
            ])

        // Breadcrumb menu
        ->set('ekyna_cms.menu.breadcrumb', MenuItem::class)
            ->factory([
                service('ekyna_cms.menu.breadcrumb_builder'),
                'createBreadcrumb'
            ])
            ->args([
                service('request_stack'),
            ])
            ->tag('knp_menu.menu', ['alias' => 'ekyna_cms.breadcrumb'])
    ;
};
