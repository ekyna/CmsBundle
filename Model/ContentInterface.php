<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface ContentInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set version
     *
     * @param integer $version
     *
     * @return ContentInterface|$this
     */
    public function setVersion($version);

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion();

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return ContentInterface|$this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return ContentInterface|$this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null);

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

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
}
