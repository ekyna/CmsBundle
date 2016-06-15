<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImagePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
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
    public function update(BlockInterface $block, Request $request)
    {
        return null;
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

    /**
     * {@inheritDoc}
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/image-plugin';
    }
}
