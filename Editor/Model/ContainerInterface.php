<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface ContainerInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ContainerInterface
    extends DataInterface,
            LayoutInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Sets the content.
     *
     * @param ContentInterface $content
     *
     * @return ContainerInterface|$this
     */
    public function setContent(ContentInterface $content = null);

    /**
     * Returns the content.
     *
     * @return ContentInterface
     */
    public function getContent();

    /**
     * Sets the copied container.
     *
     * @param ContainerInterface $copy
     *
     * @return ContainerInterface
     */
    public function setCopy(ContainerInterface $copy = null);

    /**
     * Returns the copied container.
     *
     * @return ContainerInterface
     */
    public function getCopy();

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return ContainerInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return ContainerInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the type.
     *
     * @param string $type
     *
     * @return ContentInterface|$this
     */
    public function setType($type);

    /**
     * Returns the type.
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the rows.
     *
     * @param ArrayCollection|RowInterface[] $rows
     *
     * @return ContentInterface|$this
     */
    public function setRows(ArrayCollection $rows);

    /**
     * Adds the row.
     *
     * @param RowInterface $row
     *
     * @return ContentInterface|$this
     */
    public function addRow(RowInterface $row);

    /**
     * Removes the row.
     *
     * @param RowInterface $row
     *
     * @return ContentInterface|$this
     */
    public function removeRow(RowInterface $row);

    /**
     * Returns the rows.
     *
     * @return ArrayCollection|RowInterface[]
     */
    public function getRows();

    /**
     * Returns whether or not the container is the first of the content.
     *
     * @return boolean
     */
    public function isFirst();

    /**
     * Returns whether or not the container is the last of the content.
     *
     * @return boolean
     */
    public function isLast();

    /**
     * Returns whether or not the container is the only content's child.
     *
     * @return boolean
     */
    public function isAlone();

    /**
     * Returns whether or not the container is named.
     *
     * @return boolean
     */
    public function isNamed();

    /**
     * Returns whether or not the container is titled.
     *
     * @return boolean
     */
    public function isTitled();
}
