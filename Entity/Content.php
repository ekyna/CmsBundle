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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function addBlock(BlockInterface $block)
    {
        $block->setContent($this);
        $this->blocks[] = $block;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        foreach($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->removeElement($block);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        return $this->blocks;
    }
}
