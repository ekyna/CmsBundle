<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ChainRouterPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ChainRouterPass implements CompilerPassInterface
{
    public const ROUTER_ID  = 'ekyna_cms.router';
    public const ROUTER_TAG = 'router';

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(ChainRouterPass::ROUTER_ID);

        foreach ($container->findTaggedServiceIds(ChainRouterPass::ROUTER_TAG) as $id => $attributes) {
            $priority = $attributes[0]['priority'] ?? 0;

            $definition->addMethodCall('registerRouter', [new Reference($id), $priority]);
        }

        $container->setAlias('router', ChainRouterPass::ROUTER_ID)->setPublic(true);

        $container->setAlias(RouterInterface::class, ChainRouterPass::ROUTER_ID);
        $container->setAlias(UrlGeneratorInterface::class, ChainRouterPass::ROUTER_ID);
        $container->setAlias(UrlMatcherInterface::class, ChainRouterPass::ROUTER_ID);
        $container->setAlias(RequestContextAwareInterface::class, ChainRouterPass::ROUTER_ID);
    }
}
