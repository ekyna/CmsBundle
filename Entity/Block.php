<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

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
    use RM\SortableTrait,
        RM\TimestampableTrait,
        RM\TaggedEntityTrait;

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
     * @var integer
     */
    protected $size = 12;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $data = [];


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
    public function setRow(Cms\RowInterface $row = null)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        $this->size = (int)$size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
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
     * {@inheritdoc}
     * @TODO remove as handled by plugins
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * @TODO remove as handled by plugins
     */
    public function getInitDatas()
    {
        return [
            'id'   => $this->id,
            'row'  => intval($this->row),
            'name' => intval($this->name),
            'size' => intval($this->size),
            'type' => $this->getType(),
        ];
    }
}
