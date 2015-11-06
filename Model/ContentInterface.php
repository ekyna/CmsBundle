<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityInterface;
use Ekyna\Bundle\CoreBundle\Model\TimestampableInterface;

/**
 * Interface ContentInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface extends TimestampableInterface, TaggedEntityInterface
{
    /**
     * Set blocks
     *
     * @param ArrayCollection|BlockInterface[] $blocks
     *
     * @return ContentInterface|$this
     */
    public function setBlocks(ArrayCollection $blocks);

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
     * @return ArrayCollection|BlockInterface[]
     */
    public function getBlocks();

    /**
     * Returns the indexable contents indexed by locale.
     *
     * @return array
     */
    public function getIndexableContents();
}
