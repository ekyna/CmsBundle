<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * RegistryPass
 * 
 * Adds all services with the tags "ekyna_cms.layout"
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class RegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_cms.layout_registry')) {
            return;
        }

        $definition = $container->getDefinition('ekyna_cms.layout_registry');

        $layouts = array();
        foreach ($container->findTaggedServiceIds('ekyna_cms.layout') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $layouts[$alias] = new Reference($serviceId);
        }
        $definition->replaceArgument(0, $layouts);
    }
}
