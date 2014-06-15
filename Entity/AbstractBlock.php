<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\BlockInterface;

/**
 * AbstractBlock.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractBlock implements BlockInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    protected $content;

    /**
     * @var integer
     */
    protected $row;

    /**
     * @var integer
     */
    protected $column;

    /**
     * @var integer
     */
    protected $size;


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
     * Set content
     *
     * @param \Ekyna\Bundle\CmsBundle\Entity\Content $content
     * @return Block
     */
    public function setContent(Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set row
     *
     * @param integer $row
     * @return Block
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Get row
     *
     * @return integer 
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set column
     *
     * @param integer $column
     * @return Block
     */
    public function setColumn($column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Get column
     *
     * @return integer 
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Block
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Returns the init datas for JS editor.
     * 
     * @return array
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
