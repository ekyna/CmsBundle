<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model;

/**
 * Interface ContainerInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ContainerInterface extends Model\SortableInterface, Model\TimestampableInterface, Model\TaggedEntityInterface
{
    /**
     * Set content
     *
     * @param ContentInterface $content
     * @return ContainerInterface|$this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * Get content
     *
     * @return ContentInterface
     */
    public function getContent();

    /**
     * Sets the name
     *
     * @param string $name
     * @return ContainerInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

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
     * Remove block
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
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents();
}
