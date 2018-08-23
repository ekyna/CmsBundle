<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Ekyna\Component\Resource\Model as RM;

/**
 * Interface BlockInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method BlockTranslationInterface translate($locale = null, $create = false)
 */
interface BlockInterface
    extends DataInterface,
            LayoutInterface,
            RM\TranslatableInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Set row
     *
     * @param RowInterface $row
     *
     * @return BlockInterface|$this
     */
    public function setRow(RowInterface $row = null);

    /**
     * Get row
     *
     * @return RowInterface
     */
    public function getRow();

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
     * Sets the type.
     *
     * @param string $type
     *
     * @return BlockInterface|$this
     */
    public function setType($type);

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns whether or not the block is the first of the row.
     *
     * @return boolean
     */
    public function isFirst();

    /**
     * Returns whether or not the block is the last of the row.
     *
     * @return boolean
     */
    public function isLast();

    /**
     * Returns whether or not the block is the only row's child.
     *
     * @return boolean
     */
    public function isAlone();

    /**
     * Returns whether or not the block is named.
     *
     * @return boolean
     */
    public function isNamed();
}
