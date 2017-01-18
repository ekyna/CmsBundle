<?php

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;

/**
 * Class Row
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Row implements EM\RowInterface
{
    use EM\LayoutTrait,
        RM\SortableTrait,
        RM\TimestampableTrait;

    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var EM\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection|EM\BlockInterface[]
     */
    protected $blocks;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->position = 0;
        $this->blocks = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setContainer(EM\ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function setBlocks(ArrayCollection $blocks)
    {
        foreach ($blocks as $block) {
            $this->addBlock($block);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBlock(EM\BlockInterface $block)
    {
        $block->setRow($this);
        $this->blocks->add($block);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeBlock(EM\BlockInterface $block)
    {
        $block->setRow(null);
        $this->blocks->removeElement($block);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * @inheritdoc
     */
    public function isFirst()
    {
        return 0 == $this->position;
    }

    /**
     * @inheritdoc
     */
    public function isLast()
    {
        if (null !== $this->container && ($this->container->getRows()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isAlone()
    {
        if (null === $this->container) {
            return true;
        }

        return 1 >= $this->container->getRows()->count();
    }

    /**
     * @inheritdoc
     */
    public function isNamed()
    {
        return 0 < strlen($this->name);
    }

    /**
     * @inheritdoc
     */
    public function getEntityTag()
    {
        if (0 == strlen($this->name) && null !== $this->container) {
            return $this->container->getEntityTag();
        }

        return $this->traitGetEntityTag();
    }

    /**
     * @inheritdoc
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.row';
    }
}
