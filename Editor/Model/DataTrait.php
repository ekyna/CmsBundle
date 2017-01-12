<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;

/**
 * Trait DataTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait DataTrait
{
    /**
     * @var array
     */
    protected $data = [];


    /**
     * Sets the data as key/value or whole array.
     *
     * @param string|array $keyOrData
     * @param mixed        $value
     *
     * @return DataInterface|$this
     */
    public function setData($keyOrData, $value = null)
    {
        if (is_string($keyOrData) && 0 < strlen($keyOrData)) {
            $this->data[$keyOrData] = $value;
        } elseif(is_array($keyOrData)) {
            $this->data = $keyOrData;
        } else {
            throw new InvalidArgumentException('Expected key/value or array.');
        }

        return $this;
    }

    /**
     * Unsets the data for the given key.
     *
     * @param string $key
     *
     * @return DataInterface|$this
     */
    public function unsetData($key = null)
    {
        if (is_string($key) && 0 < strlen($key)) {
            unset($this->data[$key]);
        } else {
            $this->data = [];
        }

        return $this;
    }

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
