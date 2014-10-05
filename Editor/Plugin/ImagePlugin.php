<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * Class ImagePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImagePlugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function create(array $datas = array())
    {
    	
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, array $datas = array())
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
    public function getInnerHtml(BlockInterface $block)
    {
        return '<img src="" alt="No image" />';
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return 'Ekyna\Bundle\CmsBundle\Entity\ImageBlock';
    }
}
