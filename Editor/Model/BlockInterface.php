<?php

declare(strict_types=1);

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
     * @param RowInterface|null $row
     *
     * @return BlockInterface|$this
     */
    public function setRow(RowInterface $row = null): BlockInterface;

    /**
     * Get row
     *
     * @return RowInterface|null
     */
    public function getRow(): ?RowInterface;

    /**
     * Sets the name
     *
     * @param string|null $name
     *
     * @return BlockInterface|$this
     */
    public function setName(string $name = null): BlockInterface;

    /**
     * Returns the name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return BlockInterface|$this
     */
    public function setType(string $type): BlockInterface;

    /**
     * Returns the type.
     *
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * Returns whether or not the block is the first of the row.
     *
     * @return bool
     */
    public function isFirst(): bool;

    /**
     * Returns whether or not the block is the last of the row.
     *
     * @return bool
     */
    public function isLast(): bool;

    /**
     * Returns whether or not the block is the only row's child.
     *
     * @return bool
     */
    public function isAlone(): bool;

    /**
     * Returns whether or not the block is named.
     *
     * @return bool
     */
    public function isNamed(): bool;
}
