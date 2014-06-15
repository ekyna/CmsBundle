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
     * {@inheritDoc}
     */
    public function supports(BlockInterface $block)
    {
        $class = $this->getClass();
        return $block instanceof $class;
    }
}
