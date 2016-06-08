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
     * @param BlockInterface $block
     * @param array $data
     */
    public function create(BlockInterface $block, array $data = []);

    /**
     * Updates a block.
     *
     * @param BlockInterface $block
     * @param array          $data
     */
    public function update(BlockInterface $block, array $data = []);

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
     * Returns the block content.
     *
     * @param BlockInterface $block
     *
     * @return string
     */
    public function render(BlockInterface $block);

    /**
     * Returns the supported block type.
     *
     * @return string
     */
    public function getType();
}
