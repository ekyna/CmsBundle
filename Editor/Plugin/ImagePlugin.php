<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * Class ImagePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ImagePlugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {

    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, array $data = [])
    {

    }

    /**
     * {@inheritDoc}
     */
    public function remove(BlockInterface $block)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function render(BlockInterface $block)
    {
        return '<img src="" alt="No image" />';
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return 'ekyna_cms_image';
    }
}
