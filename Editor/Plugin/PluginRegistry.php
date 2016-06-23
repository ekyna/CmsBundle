<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Editor\Exception\PluginException;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\PluginInterface as BlockPluginInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\PluginInterface as ContainerPluginInterface;

/**
 * Class PluginRegistry
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PluginRegistry
{
    /**
     * @var array
     */
    private $blockPlugins;

    /**
     * @var array
     */
    private $containerPlugins;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->blockPlugins = [];
        $this->containerPlugins = [];
    }

    /**
     * Register a plugin.
     *
     * @param BlockPluginInterface $plugin
     *
     * @throws PluginException
     */
    public function addBlockPlugin(BlockPluginInterface $plugin)
    {
        if ($this->hasBlockPlugin($plugin->getName())) {
            throw new PluginException(sprintf(
                'Block plugin named "%s" is already registered.',
                $plugin->getName()
            ));
        }
        $this->blockPlugins[$plugin->getName()] = $plugin;
    }

    /**
     * Returns whether a plugin is registered or not.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasBlockPlugin($name)
    {
        return array_key_exists($name, $this->blockPlugins);
    }

    /**
     * Returns a plugin.
     *
     * @param string $name
     *
     * @throws PluginException
     *
     * @return BlockPluginInterface
     */
    public function getBlockPlugin($name)
    {
        if (!$this->hasBlockPlugin($name)) {
            throw new PluginException(sprintf('Block plugin "%s" is not registered.', $name));
        }
        return $this->blockPlugins[$name];
    }

    /**
     * Returns the registered plugins.
     *
     * @return BlockPluginInterface[]
     */
    public function getBlockPlugins()
    {
        return $this->blockPlugins;
    }

    /**
     * Register a plugin.
     *
     * @param ContainerPluginInterface $plugin
     *
     * @throws PluginException
     */
    public function addContainerPlugin(ContainerPluginInterface $plugin)
    {
        if ($this->hasContainerPlugin($plugin->getName())) {
            throw new PluginException(sprintf(
                'Container plugin named "%s" is already registered.',
                $plugin->getName()
            ));
        }
        $this->containerPlugins[$plugin->getName()] = $plugin;
    }

    /**
     * Returns whether a plugin is registered or not.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasContainerPlugin($name)
    {
        return array_key_exists($name, $this->containerPlugins);
    }

    /**
     * Returns a plugin.
     *
     * @param string $name
     *
     * @throws PluginException
     *
     * @return ContainerPluginInterface
     */
    public function getContainerPlugin($name)
    {
        if (!$this->hasContainerPlugin($name)) {
            throw new PluginException(sprintf('Container plugin "%s" is not registered.', $name));
        }
        return $this->containerPlugins[$name];
    }

    /**
     * Returns the registered plugins.
     *
     * @return ContainerPluginInterface[]
     */
    public function getContainerPlugins()
    {
        return $this->containerPlugins;
    }
}
