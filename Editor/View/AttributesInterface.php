<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Interface AttributesInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface AttributesInterface
{
    /**
     * Clears the attributes.
     */
    public function clear(): AttributesInterface;

    /**
     * Sets the id.
     *
     * @param string|null $id
     *
     * @return AttributesInterface
     */
    public function setId(string $id = null): AttributesInterface;

    /**
     * Returns the id.
     *
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * Returns whether or not the data exists for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData(string $key): bool;

    /**
     * Sets the data.
     *
     * It will recursively replace current value if both old and new values are arrays.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return AttributesInterface
     */
    public function setData($key, $value = null): AttributesInterface;

    /**
     * Returns the data for the given key, or all data if key is omitted.
     *
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getData(string $key = null, $default = null);

    /**
     * Returns whether or not the extra exists for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasExtra(string $key): bool;

    /**
     * Sets the extra.
     *
     * @param string $key
     * @param string $value
     *
     * @return AttributesInterface
     */
    public function setExtra(string $key, string $value): AttributesInterface;

    /**
     * Returns the extra for the given key, or all extras if key is omitted.
     *
     * @param string|null $key
     * @param string|null $default
     *
     * @return string|array
     */
    public function getExtra(string $key = null, string $default = null);

    /**
     * Adds the css class.
     *
     * @param array|string $class
     *
     * @return AttributesInterface
     */
    public function addClass($class): AttributesInterface;

    /**
     * Removes the css class.
     *
     * @param array|string $class
     *
     * @return AttributesInterface
     */
    public function removeClass($class): AttributesInterface;

    /**
     * Returns the css classes.
     *
     * @return array
     */
    public function getClasses(): array;

    /**
     * Adds the css style.
     *
     * @param string $key
     * @param string $value
     *
     * @return AttributesInterface
     */
    public function addStyle(string $key, string $value): AttributesInterface;

    /**
     * Removes the css style.
     *
     * @param string $key
     *
     * @return AttributesInterface
     */
    public function removeStyle(string $key): AttributesInterface;

    /**
     * Returns the css styles.
     *
     * @return array
     */
    public function getStyles(): array;

    /**
     * Transforms to an array.
     *
     * @return array
     */
    public function toArray(): array;
}
