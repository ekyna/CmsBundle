<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EditorPluginPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EditorPluginPass implements CompilerPassInterface
{
    private const BLOCK_PLUGIN_TAG = 'ekyna_cms.editor.block_plugin';
    private const CONTAINER_PLUGIN_TAG = 'ekyna_cms.editor.container_plugin';


    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('ekyna_cms.editor.plugin_registry');

        // Block plugins
        foreach ($container->findTaggedServiceIds(self::BLOCK_PLUGIN_TAG, true) as $serviceId => $tags) {
            $registry->addMethodCall('addBlockPlugin', [new Reference($serviceId)]);
        }

        // Container plugins
        foreach ($container->findTaggedServiceIds(self::CONTAINER_PLUGIN_TAG, true) as $serviceId => $tags) {
            $registry->addMethodCall('addContainerPlugin', [new Reference($serviceId)]);
        }
    }
}
