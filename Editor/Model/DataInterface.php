<?php

declare(strict_types=1);

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
     * @return DataInterface|$this
     */
    public function setData(array|string $keyOrData, array|string|int|bool|null $value = null): DataInterface;

    /**
     * Unsets the data for the given key.
     *
     * @return DataInterface|$this
     */
    public function unsetData(string $key = null): DataInterface;

    /**
     * Returns the data.
     */
    public function getData(): array;
}
