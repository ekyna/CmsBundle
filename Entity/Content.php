<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;

/**
 * Content
 *
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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $blocks;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->blocks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
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
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
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
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
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
     * @param \Ekyna\Bundle\CmsBundle\Entity\Block $block
     * 
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    public function addBlock(Block $block)
    {
        $block->setContent($this);
        $this->blocks[] = $block;
    
        return $this;
    }

    /**
     * Set blocks
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $blocks
     * 
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
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
     * @param \Ekyna\Bundle\CmsBundle\Entity\Block $block
     * 
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    public function removeBlock(Block $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
