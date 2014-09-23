<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * ContentInterface
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface
{
    /**
     * Add block
     *
     * @param BlockInterface $block
     *
     * @return ContentInterface|$this
     */
    public function addBlock(BlockInterface $block);

    /**
     * Remove blocks
     *
     * @param BlockInterface $block
     *
     * @return ContentInterface|$this
     */
    public function removeBlock(BlockInterface $block);

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks();
}
