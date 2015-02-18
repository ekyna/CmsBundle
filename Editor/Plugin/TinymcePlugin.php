<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Entity\TinymceBlock;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    public function create(array $datas = array())
    {
    	$block = new TinymceBlock();
        $defaultContent = array_key_exists('default_content', $this->config) ? $this->config['default_content'] : '';
    	$block->setHtml('<p>' . $defaultContent . '</p>');
    	return $block;
    }

    /**
     * {@inheritDoc}
     */
    public function update(BlockInterface $block, array $datas = array())
    {
        if (array_key_exists('html', $datas)) {
            $block->setHtml($datas['html']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(BlockInterface $block) {}

    /**
     * {@inheritDoc}
     */
    public function getInnerHtml(BlockInterface $block)
    {
        return $block->getHtml();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return 'Ekyna\Bundle\CmsBundle\Entity\TinymceBlock';
    }
}
