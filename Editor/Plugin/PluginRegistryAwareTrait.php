<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

/**
 * Trait PluginRegistryAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait PluginRegistryAwareTrait
{
    /**
     * @var PluginRegistry
     */
    private $pluginRegistry;


    /**
     * Sets the plugin registry.
     *
     * @param PluginRegistry $registry
     */
    public function setPluginRegistry(PluginRegistry $registry)
    {
        $this->pluginRegistry = $registry;
    }

    /**
     * Returns the block plugin by name.
     *
     * @param string $name
     *
     * @return Block\PluginInterface
     */
    public function getBlockPlugin($name)
    {
        return $this->pluginRegistry->getBlockPlugin($name);
    }

    /**
     * Returns the container plugin by name.
     *
     * @param string $name
     *
     * @return Container\PluginInterface
     */
    public function getContainerPlugin($name)
    {
        return $this->pluginRegistry->getContainerPlugin($name);
    }
}
