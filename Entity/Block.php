<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Editor\Model as Editor;
use Ekyna\Component\Resource\Model as RM;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method Cms\BlockTranslationInterface translate($locale = null, $create = false)
 */
class Block extends RM\AbstractTranslatable implements Cms\BlockInterface
{
    use Editor\DataTrait,
        Editor\LayoutTrait,
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
     * @var Cms\RowInterface
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
     * @var array
     */
    protected $data = [];


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
    public function setRow(Cms\RowInterface $row = null)
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

    /**
     * @inheritdoc
     * @TODO remove as handled by plugins
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * @inheritdoc
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents()
    {
        return [];
    }

    /**
     * @inheritdoc
     * @TODO remove as handled by plugins
     */
    public function getInitDatas()
    {
        return [
            'id'   => $this->id,
            'row'  => intval($this->row),
            'name' => intval($this->name),
            //'size' => intval($this->size),
            'type' => $this->getType(),
        ];
    }
}
