<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EditorPluginPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EditorPluginPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_cms.editor.plugin_registry')) {
            return;
        }

        $registry = $container->getDefinition('ekyna_cms.editor.plugin_registry');

        // Block plugins
        $taggedServices = $container->findTaggedServiceIds('ekyna_cms.editor.block_plugin');
        foreach ($taggedServices as $id => $tagAttributes) {
            $registry->addMethodCall('addBlockPlugin', [new Reference($id)]);
        }

        // Container plugins
        $taggedServices = $container->findTaggedServiceIds('ekyna_cms.editor.container_plugin');
        foreach ($taggedServices as $id => $tagAttributes) {
            $registry->addMethodCall('addContainerPlugin', [new Reference($id)]);
        }
    }
}
