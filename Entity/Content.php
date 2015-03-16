<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CoreBundle\Model\TimestampableTrait;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements ContentInterface
{
    use TimestampableTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var number
     */
    protected $version;

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

    /**
     * {@inheritdoc}
     */
    public function getIndexableContent()
    {
        $content = '';
        foreach ($this->blocks as $block) {
            if ($block->isIndexable()) {
                $content .= $block->getIndexableContent() . ' ';
            }
        }
        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityTag()
    {
        if (null === $this->getId()) {
            throw new \RuntimeException('Unable to generate entity tag, as the id property is undefined.');
        }
        return sprintf('ekyna_cms.content[id:%s]', $this->getId());
    }
}
