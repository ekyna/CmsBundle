<?php

declare(strict_types=1);

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
    private const PROVIDER_TAG = 'ekyna_cms.schema_org_provider';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('ekyna_cms.schema_org.provider_registry');

        foreach ($container->findTaggedServiceIds(self::PROVIDER_TAG, true) as $serviceId => $tags) {
            $registry->addMethodCall('registerProvider', [new Reference($serviceId)]);
        }
    }
}
