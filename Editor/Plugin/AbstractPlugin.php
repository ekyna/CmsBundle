<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * AbstractPlugin.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin implements PluginInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Returns the plugin config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(BlockInterface $block)
    {
        $class = $this->getClass();
        return $block instanceof $class;
    }
}
