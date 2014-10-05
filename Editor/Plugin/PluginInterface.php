<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface
{
    /**
     * Creates a new block.
     * 
     * @param array $datas
     *
     * @return BlockInterface
     */
    public function create(array $datas = array());

    /**
     * Updates a block.
     * 
     * @param BlockInterface $block
     * @param array          $datas
     */
    public function update(BlockInterface $block, array $datas = array());

    /**
     * Removes a block.
     *
     * @param BlockInterface $block
     */
    public function remove(BlockInterface $block);

    /**
     * Returns whether the block is supported.
     * 
     * @param BlockInterface $block
     *
     * @return boolean
     */
    public function supports(BlockInterface $block);

    /**
     * Returns the inner html.
     * 
     * @param BlockInterface $block
     * 
     * @return string
     */
    public function getInnerHtml(BlockInterface $block);

    /**
     * Returns the supported block fqcn.
     * 
     * @return string
     */
    public function getClass();
}
