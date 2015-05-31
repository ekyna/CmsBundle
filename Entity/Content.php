<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityTrait;
use Ekyna\Bundle\CoreBundle\Model\TimestampableTrait;

/**
 * Class Content
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Content implements ContentInterface
{
    use TimestampableTrait;
    use TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

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
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.content';
    }
}
