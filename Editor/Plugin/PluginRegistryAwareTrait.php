<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

/**
 * Trait PluginRegistryAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait PluginRegistryAwareTrait
{
    private PluginRegistry $pluginRegistry;


    /**
     * Sets the plugin registry.
     *
     * @param PluginRegistry $registry
     */
    public function setPluginRegistry(PluginRegistry $registry): void
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
    public function getBlockPlugin(string $name): Block\PluginInterface
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
    public function getContainerPlugin(string $name): Container\PluginInterface
    {
        return $this->pluginRegistry->getContainerPlugin($name);
    }
}
