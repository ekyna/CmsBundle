<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ekyna\Bundle\CoreBundle\Model;

/**
 * Interface ContainerInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ContainerInterface extends Model\SortableInterface, Model\TimestampableInterface, Model\TaggedEntityInterface
{
    /**
     * Set content
     *
     * @param ContentInterface $content
     * @return ContainerInterface|$this
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
     * @return ContainerInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name
     *
     * @return string
     */
    public function getName();

    /**
     * Set rows
     *
     * @param ArrayCollection|RowInterface[] $rows
     *
     * @return ContentInterface|$this
     */
    public function setRows(ArrayCollection $rows);

    /**
     * Add row
     *
     * @param RowInterface $row
     *
     * @return ContentInterface|$this
     */
    public function addRow(RowInterface $row);

    /**
     * Remove row
     *
     * @param RowInterface $row
     *
     * @return ContentInterface|$this
     */
    public function removeRow(RowInterface $row);

    /**
     * Get rows
     *
     * @return ArrayCollection|RowInterface[]
     */
    public function getRows();

    /**
     * Sort the rows by position.
     *
     * @return ContentInterface|$this
     */
    public function sortRows();

    /**
     * Returns the indexable contents indexed by locale.
     *
     * @return array
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents();
}
