<?php

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
    public function clear();

    /**
     * Sets the id.
     *
     * @param string $id
     *
     * @return AttributesInterface
     */
    public function setId($id);

    /**
     * Returns the id.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns whether or not the data exists for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData($key);

    /**
     * Sets the data.
     *
     * It will recursively replace current value if both old and new values are arrays.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return AttributesInterface
     */
    public function setData($key, $value = null);

    /**
     * Returns the data for the given key, or all data if key is omitted.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getData($key = null, $default = null);

    /**
     * Returns whether or not the extra exists for the given key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasExtra($key);

    /**
     * Sets the extra.
     *
     * @param string $key
     * @param string $value
     *
     * @return AttributesInterface
     */
    public function setExtra($key, $value);

    /**
     * Returns the extra for the given key, or all extras if key is omitted.
     *
     * @param string $key
     * @param string $default
     *
     * @return string|array
     */
    public function getExtra($key = null, $default = null);

    /**
     * Adds the css class.
     *
     * @param array|string $class
     *
     * @return AttributesInterface
     */
    public function addClass($class);

    /**
     * Removes the css class.
     *
     * @param string $class
     *
     * @return AttributesInterface
     */
    public function removeClass($class);

    /**
     * Returns the css classes.
     *
     * @return array
     */
    public function getClasses();

    /**
     * Adds the css style.
     *
     * @param string $key
     * @param string $value
     *
     * @return AttributesInterface
     */
    public function addStyle($key, $value);

    /**
     * Removes the css style.
     *
     * @param string $key
     *
     * @return AttributesInterface
     */
    public function removeStyle($key);

    /**
     * Returns the css styles.
     *
     * @return array
     */
    public function getStyles();

    /**
     * Transforms to an array.
     *
     * @return array
     */
    public function toArray();
}
