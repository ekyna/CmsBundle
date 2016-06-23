<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Model\AbstractTranslatable;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use Ekyna\Bundle\CoreBundle\Model as Core;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class Block extends AbstractTranslatable implements Cms\BlockInterface
{
    use Core\SortableTrait,
        Core\TimestampableTrait,
        Core\TaggedEntityTrait;

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
        $this->size = $size;

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
            'id'     => $this->id,
            'type'   => $this->getType(),
            'row'    => intval($this->row),
            'column' => intval($this->column),
            'size'   => intval($this->size),
        ];
    }
}
