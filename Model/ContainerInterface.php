<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CmsBundle\Editor\Model as Editor;
use Ekyna\Component\Resource\Model as RM;

/**
 * Interface ContainerInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ContainerInterface
    extends Editor\DataInterface,
            Editor\LayoutInterface,
            RM\SortableInterface,
            RM\TimestampableInterface,
            RM\TaggedEntityInterface
{
    /**
     * Sets the content.
     *
     * @param ContentInterface $content
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
     * Sets the name.
     *
     * @param string $name
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
     * Returns the indexable contents indexed by locale.
     *
     * @return array
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents();
}
