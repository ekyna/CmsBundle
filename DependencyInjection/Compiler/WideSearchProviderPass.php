<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class WideSearchProviderPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class WideSearchProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_cms.wide_search')) {
            return;
        }

        $registry = $container->getDefinition('ekyna_cms.wide_search');
        $providersServices = $container->findTaggedServiceIds('ekyna_cms.wide_search.provider');

        $providers = [];
        foreach ($providersServices as $id => $tags) {
            $providers[] = new Reference($id);
        }
        $registry->replaceArgument(0, $providers);
    }
}
