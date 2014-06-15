<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * EditorPluginPass.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EditorPluginPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_cms.editor.plugin_registry')) {
            return;
        }
    
        $registry = $container->getDefinition('ekyna_cms.editor.plugin_registry');
        
        $taggedServices = $container->findTaggedServiceIds('ekyna_cms.editor.plugin');
        
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $registry->addMethodCall(
                    'register',
                    array($attributes["alias"], new Reference($id))
                );
            }
        }
    }
}
