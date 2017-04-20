<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Editor\Exception\PluginException;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\PluginInterface as BlockPluginInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\PluginInterface as ContainerPluginInterface;

/**
 * Class PluginRegistry
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PluginRegistry
{
    private array $blockPlugins;
    private array $containerPlugins;

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
    public function addBlockPlugin(BlockPluginInterface $plugin): void
    {
        if ($this->hasBlockPlugin($plugin->getName())) {
            throw new PluginException(sprintf(
                'Block plugin named "%s" is already registered.',
                $plugin->getName()
            ));
        }

        if ($plugin instanceof PluginRegistryAwareInterface) {
            $plugin->setPluginRegistry($this);
        }

        $this->blockPlugins[$plugin->getName()] = $plugin;
    }

    /**
     * Returns whether a plugin is registered or not.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasBlockPlugin(string $name): bool
    {
        return array_key_exists($name, $this->blockPlugins);
    }

    /**
     * Returns a plugin.
     *
     * @param string $name
     *
     * @return BlockPluginInterface
     * @throws PluginException
     *
     */
    public function getBlockPlugin(string $name): BlockPluginInterface
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
    public function getBlockPlugins(): array
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

        if ($plugin instanceof PluginRegistryAwareInterface) {
            $plugin->setPluginRegistry($this);
        }

        $this->containerPlugins[$plugin->getName()] = $plugin;
    }

    /**
     * Returns whether a plugin is registered or not.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasContainerPlugin(string $name): bool
    {
        return array_key_exists($name, $this->containerPlugins);
    }

    /**
     * Returns a plugin.
     *
     * @param string $name
     *
     * @return ContainerPluginInterface
     * @throws PluginException
     */
    public function getContainerPlugin(string $name): ContainerPluginInterface
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
    public function getContainerPlugins(): array
    {
        return $this->containerPlugins;
    }
}
