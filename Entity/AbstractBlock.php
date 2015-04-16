<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityTrait;

/**
 * Class AbstractBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractBlock implements BlockInterface
{
    use TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ContentInterface
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var integer
     */
    protected $row = 1;

    /**
     * @var integer
     */
    protected $column = 1;

    /**
     * @var integer
     */
    protected $size = 12;


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
    public function setContent(ContentInterface $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
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
    public function setRow($row)
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
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return $this->column;
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
    public function isIndexable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexableContent()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getInitDatas()
    {
        return array(
        	'id'     => $this->id,
            'type'   => $this->getType(),
        	'row'    => intval($this->row),
        	'column' => intval($this->column),
        	'size'   => intval($this->size)            
        );
    }
}
