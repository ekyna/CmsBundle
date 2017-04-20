<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\CmsBundle\Service\Helper\RoutingHelper;
use Ekyna\Bundle\CmsBundle\Service\Routing\FinalMatcher;
use Ekyna\Bundle\CmsBundle\Service\Routing\NestedMatcher;
use Ekyna\Bundle\CmsBundle\Service\Routing\RouteProvider;
use Ekyna\Bundle\CmsBundle\Service\Routing\Router;
use Ekyna\Bundle\CmsBundle\Service\Routing\RoutingLoader;
use Ekyna\Bundle\CmsBundle\Service\Routing\UrlGenerator;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Route provider
        ->set('ekyna_cms.routing.route_provider', RouteProvider::class)
            ->lazy(true)
            ->args([
                service('ekyna_cms.repository.page'),
                abstract_arg('Page configuration'),
                param('ekyna_resource.locales'),
            ])

        // Nested matcher
        ->set('ekyna_cms.routing.nested_matcher', NestedMatcher::class)
            ->args([
                service('ekyna_cms.routing.route_provider'),
                service('ekyna_cms.routing.final_matcher'),
            ])

        // Final matcher
        ->set('ekyna_cms.routing.final_matcher', FinalMatcher::class)
            ->args([
                inline_service(RouteCollection::class),
                inline_service(RequestContext::class),
            ])

        // Url generator
        ->set('ekyna_cms.routing.url_generator', UrlGenerator::class)
            ->args([
                service('ekyna_cms.routing.route_provider'),
                service('logger')->nullOnInvalid(),
            ])

        // Dynamic router
        ->set('ekyna_cms.routing.dynamic_router', Router::class)
            ->args([
                service('router.request_context'),
                service('ekyna_cms.routing.nested_matcher'),
                service('ekyna_cms.routing.url_generator'),
                '',
                service('event_dispatcher'),
                service('ekyna_cms.routing.route_provider'),
            ])
            ->call('setRequestStack', [service('request_stack')])

        // Routing loader
        ->set('ekyna_cms.routing.loader', RoutingLoader::class)
            ->args([
                abstract_arg('Page configuration'),
                param('kernel.debug'),
            ])
            ->tag('routing.loader')

        // Chain router
        ->set('ekyna_cms.router', ChainRouter::class)
            ->args([
                service('logger')->ignoreOnInvalid()
            ])
            ->call('setContext', [service('router.request_context')])
            ->call('add', [service('ekyna_cms.routing.dynamic_router'), 0])
            ->call('add', [service('router.default'), 64])

        // Routing helper
        ->set('ekyna_cms.helper.routing', RoutingHelper::class)
            ->args([
                service('router'),
                param('kernel.default_locale'),
            ])
    ;
};
