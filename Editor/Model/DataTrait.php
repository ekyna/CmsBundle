<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;

/**
 * Trait DataTrait
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait DataTrait
{
    protected array $data = [];


    /**
     * Sets the data as key/value or whole array.
     *
     * @param string|array $keyOrData
     * @param mixed        $value
     *
     * @return DataInterface|$this
     */
    public function setData($keyOrData, $value = null): DataInterface
    {
        if (is_string($keyOrData) && !empty($keyOrData)) {
            $this->data[$keyOrData] = $value;
        } elseif (is_array($keyOrData)) {
            $this->data = $keyOrData;
        } else {
            throw new InvalidArgumentException('Expected key/value or array.');
        }

        return $this;
    }

    /**
     * Unsets the data for the given key.
     *
     * @param string|null $key
     *
     * @return DataInterface|$this
     */
    public function unsetData(string $key = null): DataInterface
    {
        if (is_string($key) && !empty($key)) {
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
    public function getData(): array
    {
        return $this->data;
    }
}
