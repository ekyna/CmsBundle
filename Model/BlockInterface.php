<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CoreBundle\Model\TaggedEntityInterface;

/**
 * Interface BlockInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface BlockInterface extends TaggedEntityInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set content
     *
     * @param ContentInterface $content
     * @return BlockInterface|$this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * Get content
     *
     * @return ContentInterface
     */
    public function getContent();

    /**
     * Sets the name
     *
     * @param string $name
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

    /**
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     */
    public function isIndexable();

    /**
     * Returns the indexable content.
     *
     * @return string
     */
    public function getIndexableContent();
}
