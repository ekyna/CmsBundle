<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

/**
 * Interface RegistryInterface
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface RegistryInterface extends BuilderAwareInterface
{
    /**
     * Registers the given provider class.
     *
     * @param string|array $class
     */
    public function registerClass($class);

    /**
     * Registers the given provider.
     *
     * @param ProviderInterface|string $provider
     */
    public function registerProvider(ProviderInterface $provider);

    /**
     * Returns the provider for the given object class.
     *
     * @param string|object $classOrObject The object to build the schema from, or its class.
     *
     * @return ProviderInterface
     */
    public function getProvider($classOrObject);
}
