<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler\ChainRouterPass;
use Ekyna\Bundle\CmsBundle\Service\Helper\RoutingHelper;
use Ekyna\Bundle\CmsBundle\Service\Routing\ChainRouter;
use Ekyna\Bundle\CmsBundle\Service\Routing\DynamicRouter;
use Ekyna\Bundle\CmsBundle\Service\Routing\RouteProvider;
use Ekyna\Bundle\CmsBundle\Service\Routing\RoutingLoader;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Route provider
        ->set('ekyna_cms.routing.route_provider', RouteProvider::class)
            ->lazy(true)
            ->args([
                service('ekyna_cms.repository.page'),
                service('ekyna_cms.cache'),
                abstract_arg('Page configuration'),
                param('ekyna_resource.locales'),
            ])

        // Dynamic router
        ->set('ekyna_cms.routing.dynamic_router', DynamicRouter::class)
            ->args([
                service('ekyna_cms.routing.route_provider'),
                service('router.request_context'),
                service('logger')->nullOnInvalid(),
                param('kernel.default_locale'),
            ])

        // Chain router
        ->set(ChainRouterPass::ROUTER_ID, ChainRouter::class)
            ->args([
                service('router.request_context'),
                service('logger')->ignoreOnInvalid()
            ])
            ->call('registerRouter', [service('ekyna_cms.routing.dynamic_router'), 0])
            ->call('registerRouter', [service('router.default'), 64])

        // Routing loader
        ->set('ekyna_cms.routing.loader', RoutingLoader::class)
            ->args([
                abstract_arg('Page configuration'),
                param('kernel.debug'),
            ])
            ->tag('routing.loader')

        // Routing helper
        ->set('ekyna_cms.helper.routing', RoutingHelper::class)
            ->args([
                service('router'),
                param('kernel.default_locale'),
            ])
    ;
};
