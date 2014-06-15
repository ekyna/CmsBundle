<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * BlockInterface.
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface BlockInterface
{
    /**
     * Set row
     *
     * @param integer $row
     * 
     * @return Block
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
     * @return Block
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
     * @return Block
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
