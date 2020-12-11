<?php

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Component\Resource\Model as RM;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method EM\BlockTranslationInterface translate($locale = null, $create = false)
 */
class Block extends RM\AbstractTranslatable implements EM\BlockInterface
{
    use EM\DataTrait;
    use EM\LayoutTrait;
    use RM\SortableTrait;
    use RM\TimestampableTrait;

    use RM\TaggedEntityTrait {
        getEntityTag as traitGetEntityTag;
    }


    /**
     * Clones the block.
     */
    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
            $this->row = null;

            $translations = $this->translations->toArray();
            $this->translations = new ArrayCollection();
            foreach ($translations as $translation) {
                $this->addTranslation(clone $translation);
            }
        }
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var EM\RowInterface
     */
    protected $row;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;


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
    public function setRow(EM\RowInterface $row = null)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRow()
    {
        return $this->row;
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
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
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
        if (null !== $this->row && ($this->row->getBlocks()->count() - 1 > $this->position)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isAlone()
    {
        if (null === $this->row) {
            return true;
        }

        return 1 >= $this->row->getBlocks()->count();
    }

    /**
     * @inheritdoc
     */
    public function isNamed()
    {
        return 0 < strlen($this->name);
    }

    /**
     * @inheritDoc
     */
    public function getEntityTag()
    {
        if (0 == strlen($this->name) && null !== $this->row) {
            return $this->row->getEntityTag();
        }

        return $this->traitGetEntityTag();
    }

    /**
     * Returns the entity tag.
     *
     * @return string
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.block';
    }
}
