<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SchemaOrgProviderPass
 * @package Ekyna\Bundle\CmsBundle\DependencyInjection\Compiler
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SchemaOrgProviderPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ekyna_cms.schema_org.provider_registry')) {
            return;
        }

        $registry = $container->getDefinition('ekyna_cms.schema_org.provider_registry');

        $taggedServices = $container->findTaggedServiceIds('ekyna_cms.schema_org_provider');
        foreach ($taggedServices as $id => $tagAttributes) {
            $registry->addMethodCall('registerProvider', [new Reference($id)]);
        }
    }
}
