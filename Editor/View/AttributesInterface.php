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
     * Checks if an attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return bool true if the attribute is defined, false otherwise
     */
    public function has($name);

    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value if not found
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Sets an attribute.
     *
     * It will recursively replace current value if both old and new values are arrays.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value);

    /**
     * Returns attributes.
     *
     * @return array Attributes
     */
    public function all();

    /**
     * Sets attributes.
     *
     * @param array $attributes Attributes
     */
    public function replace(array $attributes);

    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove($name);
}
