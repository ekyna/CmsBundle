<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements ContentInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var number
     */
    protected $version;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection|BlockInterface[]
     */
    protected $blocks;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set version
     *
     * @param integer $version
     * 
     * @return Content
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * 
     * @return Content
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * 
     * @return Content
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add block
     *
     * @param BlockInterface $block
     * 
     * @return Content
     */
    public function addBlock(BlockInterface $block)
    {
        $block->setContent($this);
        $this->blocks[] = $block;
    
        return $this;
    }

    /**
     * Set blocks
     *
     * @param ArrayCollection|BlockInterface[] $blocks
     * 
     * @return Content
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        foreach($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * Remove blocks
     *
     * @param BlockInterface $block
     * 
     * @return Content
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Get blocks
     *
     * @return ArrayCollection|BlockInterface[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
