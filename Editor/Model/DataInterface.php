<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Interface DataInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface DataInterface
{
    /**
     * Sets the data as key/value or whole array.
     *
     * @param string|array $keyOrData
     * @param mixed        $value
     *
     * @return DataInterface|$this
     */
    public function setData($keyOrData, $value = null);

    /**
     * Unsets the data for the given key.
     *
     * @param string $key
     *
     * @return DataInterface|$this
     */
    public function unsetData($key = null);

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData();
}
