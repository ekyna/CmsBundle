<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface BlockInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface BlockInterface
{
    /**
     * Set content
     *
     * @param ContentInterface $content
     *
     * @return BlockInterface|$this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * Get content
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Content
     */
    public function getContent();

    /**
     * Sets the name
     *
     * @param string $name
     *
     * @return BlockInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

    /**
     * Set row
     *
     * @param integer $row
     * 
     * @return BlockInterface|$this
     */
    public function setRow($row);

    /**
     * Get row
     *
     * @return integer
     */
    public function getRow();

    /**
     * Set column
     *
     * @param integer $column
     * 
     * @return BlockInterface|$this
     */
    public function setColumn($column);

    /**
     * Get column
     *
     * @return integer
     */
    public function getColumn();

    /**
     * Set size
     *
     * @param integer $size
     * 
     * @return BlockInterface|$this
     */
    public function setSize($size);

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize();

    /**
     * Returns the init datas for JS editor.
     *
     * @return array
     */
    public function getInitDatas();

    /**
     * Returns the type of the block
     */
    public function getType();
}
