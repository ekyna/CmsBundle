<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Editor\Exception\UnknownPluginException;
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
     * @param string $name
     * @param BlockPluginInterface $plugin
     *
     * @throws \InvalidArgumentException
     */
    public function addBlockPlugin($name, BlockPluginInterface $plugin)
    {
        $this->blockPlugins[$name] = $plugin;
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
     * @throws UnknownPluginException
     *
     * @return BlockPluginInterface
     */
    public function getBlockPlugin($name)
    {
        if ($this->hasBlockPlugin($name)) {
            return $this->blockPlugins[$name];
        }
        throw new UnknownPluginException(sprintf('Block plugin "%s" is not registered.', $name));
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
     * @param string $name
     * @param ContainerPluginInterface $plugin
     *
     * @throws \InvalidArgumentException
     */
    public function addContainerPlugin($name, ContainerPluginInterface $plugin)
    {
        $this->containerPlugins[$name] = $plugin;
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
     * @throws UnknownPluginException
     *
     * @return ContainerPluginInterface
     */
    public function getContainerPlugin($name)
    {
        if ($this->hasContainerPlugin($name)) {
            return $this->containerPlugins[$name];
        }
        throw new UnknownPluginException(sprintf('Container plugin "%s" is not registered.', $name));
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
