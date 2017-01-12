<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

/**
 * Interface PluginRegistryAwareInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface PluginRegistryAwareInterface
{
    /**
     * Sets the plugin registry.
     *
     * @param PluginRegistry $registry
     */
    public function setPluginRegistry(PluginRegistry $registry);
}
