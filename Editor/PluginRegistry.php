<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginInterface;

/**
 * Class PluginRegistry
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PluginRegistry
{
    /**
     * @var array
     */
    private $plugins;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->plugins = array();
    }

    /**
     * Register a plugin.
     * 
     * @param string $name
     * @param PluginInterface $plugin
     * 
     * @throws \InvalidArgumentException
     */
    public function register($name, PluginInterface $plugin)
    {
        $this->plugins[$name] = $plugin;
    }

    /**
     * Returns whether a plugin is registered or not.
     * 
     * @param string $name
     * 
     * @return boolean
     */
    public function has($name)
    {
        return array_key_exists($name, $this->plugins);
    }

    /**
     * Returns a plugin.
     * 
     * @param string $name
     * 
     * @throws \InvalidArgumentException
     *
     * @return PluginInterface
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->plugins[$name];
        }
        throw new \InvalidArgumentException(sprintf('Plugin "%s" is not registered.', $name));
    }

    /**
     * Returns the registered plugins.
     * 
     * @return PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
