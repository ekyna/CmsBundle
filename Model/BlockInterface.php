<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslatableInterface;
use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Component\Resource\Model as ResourceModel;

/**
 * Interface BlockInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method BlockTranslationInterface translate($locale = null, $create = false)
 */
interface BlockInterface
    extends TranslatableInterface,
            ResourceModel\SortableInterface,
            ResourceModel\TimestampableInterface,
            Core\TaggedEntityInterface
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
     * Sets the data.
     *
     * @param array $data
     *
     * @return BlockInterface|$this
     */
    public function setData(array $data);

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData();

    /**
     * Returns the init datas for JS editor.
     *
     * @return array
     * @TODO remove as handled by plugins
     */
    public function getInitDatas();

    /**
     * Returns whether the exhibitor should be indexed or not by elasticsearch.
     *
     * @return bool
     * @TODO remove as handled by plugins
     */
    public function isIndexable();

    /**
     * Returns the indexable contents indexed by locales.
     *
     * @return array
     * @TODO remove as handled by plugins
     */
    public function getIndexableContents();
}
